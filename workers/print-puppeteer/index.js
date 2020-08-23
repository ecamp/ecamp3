const puppeteer = require('puppeteer');
const amqp = require('amqplib/callback_api');

async function startBrowser() {
    const browser = await puppeteer.launch({headless: true, args:['--no-sandbox']});
    const page = await browser.newPage();
    return {browser, page};
}

async function closeBrowser(browser) {
    return browser.close();
}

async function html2pdf(url, filename, sessionId) {
    const {browser, page} = await startBrowser();

    const cookies = [
        {
            "domain": "print",
            "hostOnly": true,
            "httpOnly": false,
            "name": "PHPSESSID",
            "path": "/",
            "sameSite": "unspecified",
            "secure": false,
            "session": true,
            "storeId": "1",
            "value": sessionId,
            "id": 1
        }
    ]

    await page.setCookie(...cookies)
    // page.setCacheEnabled(false)
    await page.goto(url);

    await page.waitFor(500);
    await page.waitForSelector(".pagedjs_pages", {timeout:2000});

    await page.pdf({path: `data/${filename}.pdf`});
}

amqp.connect('amqp://rabbitmq', function(error0, connection) {
    if (error0) {
        throw error0;
    }
    connection.createChannel(function(error1, channel) {
        if (error1) {
            throw error1;
        }

        var queue = 'printer-puppeteer';

        channel.assertQueue(queue, {
            durable: true
        });

        console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", queue);

        channel.consume(queue, async function(msg) {
            console.log(" [x] Received %s", msg.content.toString());
            const message= JSON.parse(msg.content.toString());
            
            await html2pdf(`http://print:3000/?camp=${message.campId}&pagedjs=true`, message.filename, message.PHPSESSID);

        }, {
            noAck: true
        });
    });
});