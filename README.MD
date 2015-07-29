# MobWeb_QuotationRequestForm extension for Magento

This extension enables a new form under **/quote**. This form can be filled out by a customer and will then be sent to the specified email address. The email contains all of the products that are currently in the customer's cart, along with the rest of the data from the form.

This way, the customer can ask for a quotation for all the products currently in their cart. The admin can see all the cart products in the email and can then create a custom quotation for this customer.

## Installation

Install using [colinmollenhour/modman](https://github.com/colinmollenhour/modman/).

You will also have to add links to **/quote** from wherever you see fit, for example from the cart or the header.

## Configuration

Go to **System -> Configuration -> (Sales) -> Quotation Request Form** to specify the recipient of the quotation request email.

You may also create a new static block with the identifier of **quotation_request_form_introduction** which will then be displayed above the quotation request form.

## Questions? Need help?

Most of my repositories posted here are projects created for customization requests for clients, so they probably aren't very well documented and the code isn't always 100% flexible. If you have a question or are confused about how something is supposed to work, feel free to get in touch and I'll try and help: [info@mobweb.ch](mailto:info@mobweb.ch).
