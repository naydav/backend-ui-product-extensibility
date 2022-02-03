### Testcase:

Implement way for adding additional sections on a product edit page in the Adobe Commerce admin panel.
The content should be loaded from external services implented in App Builder Runtime Actions.
As example, should be added sections that shows locations of warehouses where the product is in stock.

### Details of implementation:
**Adobe Commerce admin panel**
Adobe Commerce makes HTTP call to Extension Data Service with product SKU and an instance context.
The result of this call should be HTML fragments for additional product editor sections (tabs on a Product Edit Page).
After retrieving content, it should be be displayed immediately in a related section.

**Extension Data Service (composite)**
The srevice provides a HTTP entry point for fetching **all additional** product data.
The service gets registered (via some configuration) extensions for specified Adobe Commerce instance.
For each extension, Extension Data Service calls the Specific Extension Data Service (leaf) to get content (It's like one-level Composite Pattern).
The service should be implemented in a App Builder Runtime Action.
The result should be an object that contains fragments of HTML retrieved from registered (leaf) extension's services.

**Specific Extension Data Service (leaf)**
The srevice provides a HTTP entry point for fetching **specific** product data related to concrete domain area. For example, Google Maps service or Magento Order Management service.
Specific Extensions Services are each separate App Builder Runtime Action.
The result should be an object with a "content" property that is some HTML snippet for displaying.

**Google Maps service**
Google Maps service is one of Specific Extension Data Service (leaf) which provides data about geolocation.
First draft will use warehouse (Multi-Source Inventory) locations.
The service uses product SKU to retrieve product object from Adobe Commerce by RESTAPI (warehouses are related to admin part that doesn't covered by GraphQL).
After that, the service calls Google Maps API itself with each warehouse address, and receives a geolocation.
The result should be an object with a "content" property that is the HTML snippet for rendering map.

### Notices:
- Need to think about naming - Extension Data Service?
- Does the product editor page call extension service during server-side layout? Or lazy load it in the browser on demand?
- Later drafts may use geodata in product image EXIFs, maybe from Asset Cloud

### Jira ticket
https://jira.corp.adobe.com/browse/DEVX-1939
