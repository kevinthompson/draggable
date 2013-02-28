
Draggable
==============================

Draggable adds drag and drop sorting to custom channel fields, member fields, statuses, and categories in the [ExpressionEngine](http://www.expressionengine.com) control panel.

## Installation

To install Draggable:

1. Copy the `/draggable/` folder to your `/system/expressionengine/third_party/` folder.
2. Navigate to "Add-Ons" > "Extensions", then click the "Enable?" link to the right of the Draggable extension listing.
3. Make sure "install" is selected for both the Accessory and the Extension and click "Submit"

Once installed, you'll be able to sort entries on the custom channel fields, member fields, statuses, and categories pages by dragging and dropping the table rows. Row order is saved automatically when you drop the row in its new position.

## ChangeLog

### 1.4.1
- Add support for DevDemon Udpater

### 1.4
- Revise order column hiding logic to accurately hide the correct column regardless of ExpressionEngine version or language
- Add visible drag handles to tables that include Draggable
- Improve extension compatibility with newer versions of ExpressionEngine
- Update extension to include all required settings as defined in the ExpressionEngine developer documentation
- Add support for sorting custom category fields
- Improved row styles while in dragging state
- Updated zip file format to be compatibile with DevDemon Updater
- Add the abilty to drag and drop categories in the edit categories feature of the publish section's categories tab

### 1.3
- Added sorting to custom member fields
- Fixed bug that was causing Field Name to be hidden in ExpressionEngine 2.2x
- Removed extension settings
- Draggable tab and order columns are now always hidden by default

### 1.2
- Various Bug Fixes