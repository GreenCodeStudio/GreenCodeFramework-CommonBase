const BaseSeleniumTest = require("../../../E2eTests/Test/Selenium/baseSeleniumTest");
const {Key} = require("selenium-webdriver");

module.exports = class extends BaseSeleniumTest {
    constructor(driver) {
        super(driver);
    }

    async mainTest() {
        const menuItems = await this.driver.findElements(By.css('[data-views="aside"] nav a'))
        for (const menuItem of menuItems) {
            await menuItem.sendKeys(Key.RETURN);
            await this.takeScreenshot('menuItems')
        }
    }
}