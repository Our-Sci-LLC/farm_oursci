# Farm OurSci Multitenant

Contains farmOS customizations for OurSci.

## Requirements
This module requires the following modules:

- [FarmOS](https://www.drupal.org/project/farm)

## Installation

Install as you would normally install a contributed Drupal module. Visit: [Installing Modules](https://www.drupal.org/docs/extending-drupal/installing-modules)
for further information.

## Configuration
To use the JSON:API consumer you must:
- Create a keys directory at the same level as the web root directory i.e. ../keys
- Generate public and private oauth keys at /admin/config/people/simple_oauth
- Create a user and give it the `jsonapi_crud` role.
- Edit the **OurSci Consumer** at /admin/config/services/consumer, set the **New Secret** and add the user to **Client Credentials** settings.
- Make a call to the `/oauth2/token` endpoint to get an access token. Use the following parameters:
  - `grant_type`: `client_credentials`
  - `client_id`: `oursci_crud`
  - `client_secret`: the secret you set in the consumer settings
  - `scope`: `jsonapi_crud`
- Use the access token in the `Authorization` header of your requests to the JSON:API
