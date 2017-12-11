# Public-Links API

Create and manage the sharing (of a document or collection) via a Public Link. A public link, by definition, is accessible by everyone that has received the link, even if is not a registered user.


Public Links are modeled via morph relation with the Shared entity.

Public links have the following fields

- user_id: the user who created the link
- id: the unique identifier of the link
- slug: the human friendly identifier

Expiration and the target resource are handled via the attached Share.

You cannot have a public Link without creating a Share, because the PublicLink is the target of the share, i.e. you are sharing a resource to who has access to the PublicLink.

A link is based on the user, so one user can only have one link to a specific resource, but multiple users can have their own links to the same resource.

## Management

### create 

JSON POST to /links

parameters:

- `to_id`: The resource identifier for which the link will be created (integer)
- `to_type`: The resource type, currently only `document` or `collection`
- `expiration`: (optional) Use this field if the link should expire on a specific day. The date must be greater than now (day + time)
- `slug`: (optional) Use this field to add a rememberable and custom url. This must contain only letters, number and dashes and must be <= 250 charactes long. It will be used to represent the link like https://dms.klink.asia/s/my-custom-slug. Also the slug must be unique in a given K-Box instance.

Optional parameter can be omitted entirely from the json, if are in the request then they should have an acceptable value.

_If you want to create public links for multiple resources you have to invoke several times this endpoint._

response: 201 if resource is created, and in the body you will be able to see the created share and public link

### Update

JSON PUT /links/{id}

A public Link can be updated, but only with respect to its expiration date and slug. Changing the resource pointed by the link is only possible by deletion and creation of a new link.

Parameters:

- `expiration`: (optional) Use this field if the link should expire on a specific day. The date must be greater than now (day + time)
- `slug`: (optional) Use this field to add a rememberable and custom url. This must contain only letters, number and dashes and must be <= 250 charactes long. It will be used to represent the link like https://dms.klink.asia/s/my-custom-slug. Also the slug must be unique in a given K-Box instance.

### Delete

DELETE /links/{id}

Deleting a link, by its unique identifier, or deleting a share via the share delete endpoint generates the same result. The link is removed and also the referring share is removed, because a PublicLink cannot exists on its own.

## Getting to the document

The public link URL is in the format

```
/s/{ID||slug}
```

This URL points to the document preview. Considering how the current implementation for obtaining a document preview works the URL is handled with a redirect to the corresponding `klink_api` route, i.e. `klink/{id}/preview`. **This is not available for collections**
