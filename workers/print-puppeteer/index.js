const puppeteer = require('puppeteer');
const amqp = require('amqplib/callback_api');

const PRINT_SERVER = process.env.PRINT_SERVER || "http://print:3000";

const AMQP_HOST = process.env.AMQP_HOST || 'rabbitmq';
const AMQP_PORT = process.env.AMQP_PORT || '5672';
const AMQP_VHOST= process.env.AMQP_VHOST || '/';
const AMQP_USER = process.env.AMQP_USER || 'guest';
const AMQP_PASS = process.env.AMQP_PASS || 'guest';


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

    await page.pdf({path: `data/${filename}-puppeteer.pdf`});
}

function start() {
    amqp.connect(`amqp://${AMQP_USER}:${AMQP_PASS}@${AMQP_HOST}:${AMQP_PORT}${AMQP_VHOST}`, function(error0, connection) {
        if (error0) {
            console.error("[AMQP] Connection error", error0.message);
            return setTimeout(start, 1000);
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

                try{
                    await html2pdf(`${PRINT_SERVER}?camp=${message.campId}&pagedjs=true`, message.filename, message.PHPSESSID);
                } catch(e){
                    console.log("Error while processing", e)
                }

                channel.ack(msg)
            }, {
                noAck: false
            });
        });
    });
}

start();