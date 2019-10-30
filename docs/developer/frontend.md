# Frontend

> Javascript, CSS and their management process.

The K-Box frontend is a custom made solution that follows (or go into the direction of) [utility-first CSS](https://johnpolacek.github.io/the-case-for-atomic-css/)  for the stylesheet. The javascript, that enables the interaction with the user, consists in a custom set of libraries that are glued together and loaded using [RequireJS](https://requirejs.org/).


- [Browser support](#browser-support)
- [The build process](#the-build-process)
- [Stylesheet](#stylesheet)
- [Javascript](#javascript)

## Browser support

We aim to support the following browsers:

- Microsoft Edge (desktop/mobile)
- Chrome (desktop/mobile, last 2 versions)
- Firefox (desktop/mobile, last 2 versions)
- Safari (desktop/mobile, last 2 versions)
- Opera (desktop/mobile, last 2 versions)
- IE11 (desktop)

We know that browser support is difficult, but we think that all users should receive a somehow usable experience. To this aim we try to preserve some degree of usability on IE9 and IE10 by using progressive enhancements.

## The build process

We cannot write directly minified Javascript or optimized CSS code so we have a build process in place.

The goals are:

- Generate vendor CSS and Javascript files
- Generate application specific CSS and Javascript files
- Keep in sync RequireJS modules
- Minify stylesheets and purge unused classes
- Apply cache busting techniques

We use [Gulp 3.9](https://gulpjs.com/) on top of [Laravel Elixir](https://github.com/laravel/elixir) (in particular we use a [forked version of Elixir](https://github.com/avvertix/elixir) to ensure compatibility with Node 11). This project started when those tools were bleeding edge, but soon everyone moved to [Laravel Mix](https://laravel-mix.com) on top of Webpack. Unfortunately due to RequireJS we are not able to migrate to Webpack yet.

> The setup is compatible with **Node 11** and might break on newer NodeJS version due to dependencies of Gulp 3.9.

As stated in the [developer installation](./developer-installation.md) guide, the compiled files for 
the frontend are not commited in the GIT repository. 

```bash
yarn
# or npm install

yarn development
# or npm run development
```

> If you want to continuously build the assets on save execute `yarn watch`

> Production assets can be generated using `yarn production`

> If you want to have a look at the build steps check [`gulpfile.js`](../../gulpfile.js)


### Frontend Folder structure

Frontend folders follows the Laravel folder structure: source files are stored under the `resources/assets` folder, while builds are created in the `public` folder.

```
|-- app
|-- public
|   |-- build *
|   |-- images
|   |-- js *
|   |    +-- modules *
|-- resources
|   |-- assets
|   |    |-- js
|   |    |    |-- deps **
|   |    |    +-- modules
|   |    |-- css
|   |    |    +-- components
|   |    +-- vendor
```

Folders marked with `*` are managed by the build process.

`**` the `deps` folder stores dependencies that are not available on NPM or has been changed from what is available in the official repository.

All the new javascript source code must be put inside the `resources/assets/js` folder according to the contribution rules specified in the [javascript](#javascript) section of this page.

All the new stylesheet source code must be put inside the `resources/assets/css` folder according to the contribution rules specified in the [stylesheet](#stylesheet) section of this page.

**Why there is a `less` folder and some less files are still in use?**

There is an on-going effort to migrate all pre-existing styles to utility-first. 
Some LESS files might still be used or kept for reference.

## Stylesheet

The stylesheet for the K-Box is assembled using PostCSS and enhanced by [Tailwind CSS](https://tailwindcss.com/).
The main style entrypoint is [`resources/assets/css/app-evolution.css`](../../resources/assets/css/app-evolution.css), while Tailwind configuration
is located in [`tailwind.config.js`](../../tailwind.config.js).

To facilitate the migration from LESS we created various components styles. The various components are imported, using PostCSS imports, in the 
main style file (i.e. `app-evolution.css`). 

If you have a necessity to add new style, please consider to do it following the utility-first approach. If you are in doubt on how to do it
properly feel free to ask in the issue (or pull request) and have a look at the [Tailwind CSS screencasts](https://tailwindcss.com/screencasts/).

> When in watch mode our build process is not able to watch files imported into `app-evolution.css`, so you need so save also 
that file to trigger a rebuild of the style.


**You said to follow the utility-first approach, but some of the code still follows BEM or other sort of naming convention**

Yes, you're right. We still have some code that do not follow the utility-first 
approach as it was migrated from LESS without refactoring.
We appreciate a lot your help in doing the refactoring.


## Javascript

Static dependencies are managed through Yarn and stored in the `node_modules` folder, while K-Box specific javascript files are in `resources\assets\js`.

Dynamic module loading is performed client side using RequireJS. 
The specific RequireJS configuration can be found in the [`require-config.blade.php`](../../resources/views/require-config.blade.php) template.

> For a list of the latest dependencies with the respective version constraints please refer to [`package.json`](../../package.json)

Dynamic dependencies and scripts should respect the AMD specification as imposed by RequireJS. All the dynamic dependendencies must be loaded with RequireJS.

In the normal context of execution you will have access to the following preloaded modules (the name of the module is what needs to be used to require it):

- `jquery`
- `DMS`
- `lodash`
- `nprogress`
- `combokeys`
- `modernizr`
- `context`


### DMS Javascript API

The DMS javascript API offer helper methods to interact with the K-Box services. 

Feature offered:

- Ajax calls to specific services updated according to the backend changes
- Navigation
- Modal Windows and Dialogs
- Loading progress and long running messages

The DMS object is available through requirejs and is named `DMS`:

```js
require(['DMS'], function(DMS){

	// ... your code here ...
    
});
```

What's inside the DMS object:

- `DMS.Ajax`: methods for making get, post, put, delete requests via Ajax;
- `DMS.Paths`: constants for services URL;
- `DMS.MessageBox`: modal window related methods
- `DMS.Utils`: some utilities
- `DMS.Progress`: for showing and hiding the overall progress bar
- `DMS.Services`: the DMS exposed services utility methods
- some navigation helper methods.

> The source code is located in [`resources/assets/js/dms/init.js`](../../resources/assets/js/dms/init.js)

#### Navigation helper methods and `DMS.Utils`

`DMS.navigate(path:String, getParams:Object, full:boolean)`

Perform a page navigation to the specified `path`. `path` could be a relative path to the current DMS instance location, or an absolute URL. If set as an absolute url set the `full` parameter to `true`.

`getParams` is an object describing the parameter that must be added to the get request. Parameter serialization is performed by [`jQuery.param()`](http://api.jquery.com/jquery.param/#jQuery-param-obj) function.

`DMS.navigateReload()`

Reloads the current page

`DMS.Utils.countKeys(obj:object) : Number`

Will count how may keys are in the specified object, `obj`.

`DMS.Utils.inArray(arr:array, what:object|string) : boolean`

Determine if the specified `what` is in the array `arr`. The implementation uses [Array.prototype.indexOf](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/indexOf) if available, otherwise will fallback to [jQuery.inArray](http://api.jquery.com/jquery.inArray/).

##### DMS.Ajax

DMS Ajax includes some simple wrapper around jQuery.ajax method that will configure the needed parameters to pass the request validation performed by the backend and to get the response in JSON format.

The DMS.Ajax has the following functions:

- `get(url, params, success, error)` perform a get request
- `post(url, params, success, error)`  perform a post request
- `put(url, params, success, error)`  perform a put request
- `delete(url, success, error)`  perform a delete request

every function returns the jqXHR object, the `params` parameter is an object that will be passed as payload to the Ajax request. `success` is the jQuery success callback, while `error` is the jQuery error callback.


##### DMS.Paths

Store the constants that defines the DMS routes for the backend API services.

- `DMS.Paths.STARRED`: 'documents/starred' endpoint route
- `DMS.Paths.SEARCH`: 'search' endpoint route
- `DMS.Paths.DOCUMENTS`: 'documents' endpoint route
- `DMS.Paths.UPLOAD_FALLBACK`: 'documents/create' endpoint route
- `DMS.Paths.GROUPS`: 'documents/groups' endpoint route
- `DMS.Paths.GROUPS_CREATE`: 'documents/groups/create' endpoint route
- `DMS.Paths.GROUPS_EDIT`: 'documents/groups/{ID}/edit' endpoint route. {ID} parameter required. To use it replace('{ID}', 'your_value')
- `DMS.Paths.SHARES`: 'shares'  endpoint route
- `DMS.Paths.SHARE_CREATE`: 'shares/create' endpoint route
- `DMS.Paths.STORAGE_REINDEX_ALL`: 'administration/storage/reindex-all' endpoint route
- `DMS.Paths.USER_PROFILE_OPTIONS`: 'profile/options' endpoint route
- `DMS.Paths.MAP_SEARCH`: 'visualizationdata' endpoint route. This is used only by the map visualization

`DMS.Paths.fullUrl(path:String) : String`

Returns the absolute URL for the given path according to the current DMS instance base URL.

##### DMS.MessageBox

- `DMS.MessageBox.success: function(title:string, text:string) : Promise`: shows a succesfull message
- `DMS.MessageBox.error: function(title:string, text:string) : Promise`: shows an error message
- `DMS.MessageBox.warning: function(title:string, text:string) : Promise`: shows a warning message
- `DMS.MessageBox.show: function(title:string, text:string) : Promise`: shows a generic modal window
- `DMS.MessageBox.close: function() : Promise`: close the current opened modal window
- `DMS.MessageBox.wait: function(title, text) : Promise`: shows a modal windows that cannot be closed from the UI as a wait/loading indicator
- `DMS.MessageBox.deleteQuestion: function(title, text, options) : Promise`: 
- `DMS.MessageBox.question: function(title, text, cofirmBtnText, cancelBtnText, callback) : Promise`: 
- `DMS.MessageBox.prompt: function(title, text, placeholder, callback) : Promise`: 

See SweetAlert2 for a the specific documentation of the options parameter and the returned Promise


##### DMS.Progress

`DMS.Progress.start()`

Start showing the overall progress bar on top of the page.

`DMS.Progress.done()`

Completes and hides the overall progress bar on top of the page.


##### DMS.Services

for required and optional parameters of a specific service please refer to the backend API page

`DMS.Services.Starred`

Handle starring and unstarring document descriptors

`add(data:object, success:callback, error:callback)` 

add a star to a document descriptor. The user is inferred by the current session.

The data object has the following keys:
- `institution:String`: the K-Link Institution identifier of the document descriptor;
- `descriptor:String`: the K-Link Local Document Id of the document descriptor;
- `visibility`: the visibility of the document descriptor to star. Could be`public` or `private`.

`remove(starId:number, success, error)`

Deletes a star, identified by its `starId`

`DMS.Services.Bulk`

Bulk operations

`remove(data, success, error)`

remove documents

`copyTo(data, success, error)`

copy documents to collection

`restore(data, success, error)`

restore documents from trash

`makePublic(data, success, error)`

make documents publicly available on the K-Link Network

`DMS.Services.Documents`

`update(id, data, success, error)`

updates a document descriptor

`remove(id, success, error)`

remove a document descriptor (put in trash)

`openEditPage(id)`

open document descriptor edit page

`visualizationSearch(arg_obj, success, error)`

used by the map visualization

`DMS.Services.Groups`

Handle operation on Collections

- `create(data, success, error)`
- `update(id, data, success, error)`
- `remove(id, success, error)`
- `open(id)`


`DMS.Services.Shared`

Handle operation inside the shared section

`openGroup(id)`

`remove(id, success, error)`


`DMS.Services.Options`

Actions related to user's options

`saveListStyle(style, success, error)` 

Change the option for the documents list visualization.

the `style` parameter could have one of the following values `details`, `tiles` or `cards`

### Modules

#### Panels

Handle show/hide for details panels and dialog screen. See `resources/assets/js/modules/panel.js`.
