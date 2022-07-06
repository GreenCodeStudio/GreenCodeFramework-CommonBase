const BaseSeleniumTest = require("../../../E2eTests/Test/Selenium/baseSeleniumTest");
const {Key, By} = require("selenium-webdriver");

module.exports = class extends BaseSeleniumTest {
    constructor(driver) {
        super(driver);
    }

    async login(driver) {
        await this.openURL('/')
        await this.asleep(1000);
        await this.takeScreenshot('loginPage', true);
        await this.driver.findElement(By.css('[name="username"]')).sendKeys(process.argv[2]);
        await this.driver.findElement(By.css('[name="password"]')).sendKeys(process.argv[3]);
        await this.asleep(1000);
        await this.takeScreenshot('loginPage-written');
        await this.driver.findElement(By.css('[name="password"]')).sendKeys(Key.RETURN);
        await this.asleep(1000);
        await this.takeScreenshot('loginPage-pressedLoginButton', true);
    }

    async prepareTest() {
        await this.login();
    }

    async mainTest() {
        this.assert(await this.driver.findElements(By.css('[data-views="aside"] nav')))
        const menuItems = await this.driver.findElements(By.css('[data-views="aside"] nav a'))
        for (const menuItem of menuItems) {
            await menuItem.sendKeys(Key.RETURN);
            await this.asleep(1000);
            await this.takeScreenshot('menuItems-' + await menuItem.getText())
        }
    }
}