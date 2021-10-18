import {BaseSeleniumTest} from "../../../E2eTests/Test/Selenium/baseSeleniumTest";
import {Key} from "selenium-webdriver";

export default class extends BaseSeleniumTest {
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