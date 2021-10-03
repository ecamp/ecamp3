const puppeteer = require('puppeteer');
const amqp = require('amqplib/callback_api');
const Sentry = require("@sentry/node");

const { PRINT_SERVER, SHARED_COOKIE_DOMAIN, SENTRY_WORKER_PRINT_PUPPETEER_DSN, AMQP_HOST, AMQP_PORT, AMQP_VHOST, AMQP_USER, AMQP_PASS } = require('./environment.js');

if (SENTRY_WORKER_PRINT_PUPPETEER_DSN) {
    Sentry.init({ dsn: SENTRY_WORKER_PRINT_PUPPETEER_DSN })
}

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
            "domain": SHARED_COOKIE_DOMAIN,
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

    await page.waitForTimeout(15000);
    //await page.waitForSelector(".pagedjs_pages", {timeout:15000}); // TODO: easy to fail, if response takes longer than expected

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

            const queue = 'printer-puppeteer';

            channel.assertQueue(queue, {
                durable: true
            });

            console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", queue);

            channel.consume(queue, async function(msg) {
                try {
                    console.log(" [x] Received %s", msg.content.toString())
                    const message= JSON.parse(msg.content.toString())

                    const configGetParams = Object.entries(message.config).map(([key, val]) => `${key}=${val}`).join('&')

                    await html2pdf(`${PRINT_SERVER}?camp=${message.campId}&pagedjs=true&${configGetParams}`, message.filename, message.PHPSESSID)
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