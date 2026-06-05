from playwright.sync_api import sync_playwright
import time

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Capture console logs to see what error Vue throws
    page.on("console", lambda msg: print(f"Browser Console: {msg.type}: {msg.text}"))
    page.on("pageerror", lambda err: print(f"Browser Uncaught Exception: {err}"))
    page.on("response", lambda resp: print(f"Network: {resp.request.method} {resp.url} - Status: {resp.status}"))

    print("Navigating to Devotee Login...")
    page.goto("http://127.0.0.1:8002/devotee/login")

    print("Filling credentials...")
    page.fill('input[type="tel"]', "9087021592")
    page.fill('input[type="password"]', "ashish")
    page.click('button:has-text("Sign In")')

    time.sleep(2)
    print("Navigating to Profile...")
    page.goto("http://127.0.0.1:8002/profile")
    time.sleep(1)

    print("Clicking Edit...")
    page.click('button:has-text("Edit")')
    time.sleep(1)

    print("Filling new Date of Birth (8 digits)...")
    page.fill('input[placeholder="DD-MM-YYYY"]', "08022018")

    print("Clicking Save Changes...")
    page.click('button:has-text("Save Changes")')
    
    print("Waiting for response...")
    time.sleep(3)
    
    # Save a screenshot to see if it's blank
    page.screenshot(path="profile_error_screenshot.png")
    print("Screenshot saved to profile_error_screenshot.png")
    
    browser.close()

with sync_playwright() as playwright:
    run(playwright)
