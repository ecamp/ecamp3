const puppeteer = require('puppeteer');

async function startBrowser() {
    const browser = await puppeteer.launch({headless: true, args:['--no-sandbox']});
    const page = await browser.newPage();
    return {browser, page};
}

async function closeBrowser(browser) {
    return browser.close();
}

async function html2pdf(url) {
    const {browser, page} = await startBrowser();
    await page.goto(url);
    await page.emulateMedia('screen');
    await page.pdf({path: 'data/page.pdf'});
}

(async () => {
    await html2pdf("http://print:3000");
    process.exit(0);
})();