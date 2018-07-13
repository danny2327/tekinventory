# tekinventory
Tekworks Inventory Database

Site Overview
This website allows you to track, add and mark as sold all of the items that Tekworks uses

There are items, itemtypes, and collections: Item Types are categories of items, for instance an ethernet cable, a video card or a stick of ram. You define a set of properties for each item type, such as a length, model or BUS Interface, and choose a corresponding "data type". This will be one of the following:

    String: Words, letters or a sentence
    Number: Any integer or 0
    Decimal: Any decimal number or 0
    Boolean: A Yes/No or True/False value, ie A router: wireless would be a boolean.

Items are of an item type and you will fill in all of the properties of that item type

Collections are groups of items, and their corresponding quantities. The warehouse is the default collection, and a collection should be created for each job, as well as employee's car kits. A collection can be archived, meaning items in that collection are marked as sold, and any unused items will be put back in the warehouse. A collection can be 'protected' meaning that it can not be archived.

Menu Overview
The Inventory button lets you manage the items in the inventory
New Item: Define a new item, choose the type, and enter properties
New Item Category: Define an Item Type including required properties and their types
View Inventory: See all inventory, regardless of collection
View Category: See all of the items of a particular Item Type
Add Stock: Increase the quantity of particular item[s] after receiving an order
View Low Stock: Will show a list of items that have hit their preset thresholds, and need to be reordered

The Collections button allows you manage the collections
New Collection: Define a new Collection, such as an employee's car or job name
View Collection: Select a collection and
Edit Collection: Edit the name/protected status of a collection
Archive Collection: Select a collection, decide what to do with any items in it, and then delete it.

The Administration button allows you administer the site. Only seen if user is an administrator
New User: Create a new User
Manage Users: View, edit and delete users
Reports: Access the various reports
