@if(app()->getLocale() == 'ru')

	<h1>Пожалуйста обновите свой браузер.</h1>

	<p>Браузер - это программа, которая дает пользователю возможность просмотривать Интернет сайты.<br/>
	Использование устаревших версий браузера подвергает Вашу личную информацию различным рискам и угрозам.</p>

	<p>В K-Box используются самые современные технологии, и чтобы полноценно их использовать нужен современный и обновленный браузер.</p>

	<p>Вы можете обновлять Ваш браузер при уведомлениях Вашей операционной системы.<br/>
	Если Вы или отдел информационных технологий не может обновить браузер, используйте альтернативный браузер.</p>

@else

	<h1>Please keep your browser up-to-date</h1>

	<p>The web browser is the connection between you and the internet. <br/>
	Using an older browser exposes you to threats and risks for your personal data.</p>

	<p>We promote the usage of modern technologies, therefore to be able to use all the 
	features available a modern and updated browser is needed.</p>

	<p>You can keep your browser updated following your Operating System upgrade notices. <br/>
	If you are not able to upgrade your current browser, or your IT department won't do that, 
	you can switch to an alternative browser.</p>

@endif

<div class="flex justify-between mb-8">

	<a class="max-w-xs inline-block text-center mr-4" href="https://www.microsoft.com/en-us/edge?form=MY01BV&OCID=MY01BV">
		<img class="mx-auto" src="{{ url('/') }}/images/edge_64x64.png" />
		Edge
	</a>

	<a class="max-w-xs inline-block text-center mr-4" href="https://www.mozilla.com/firefox/">
		<img class="mx-auto" src="{{ url('/') }}/images/firefox_64x64.png" />
		Firefox
	</a>

	<a class="max-w-xs inline-block text-center mr-4" href="https://www.google.com/chrome/browser/desktop/">
		<img class="mx-auto" src="{{ url('/') }}/images/chrome_64x64.png" />
		Chrome
	</a>

	<a class="max-w-xs inline-block text-center mr-4" href="http://www.opera.com/">
		<img class="mx-auto" src="{{ url('/') }}/images/opera_64x64.png" />
		Opera
	</a>

</div>


@if(app()->getLocale() == 'ru')

	<h2>Почему я должен обновлять браузер?</h2>

	<h3>Безопасность</h3>

	<p>Обновленные браузеры лучше защищают от вирусов, мошеничества и других угроз. В старых версиях могут быть недостатки в системе безопасности, которые исправляются в обновленных версиях.</p>

	<h3>Совместимость и новые технологии</h3>

	<ul class="mb-4">
		<li>Вы будете использовать улучшенный K-Box.</li>
		<li>Вы сможете посещать сайты, использующие последние технологии.</li>
	</ul>

	<h3>Улучшенное и удобное пользование</h3>

	<p>Используйте более удобный сервис, с новыми возможностям, расширениям и улучшенным настойкам.</p>

@else

	<h2>Why do I need an up-to-date browser?</h2>

	<h3>Security</h3>

	<p>Newer browsers protect you better against viruses, scams and other threats. Outdated browsers have security holes which are fixed in updates.</p>

	<h3>Compatibility & new Technology</h3>

	<ul class="mb-4">
		<li>You can use the latest K-Box enhancements.</li>
		<li>You can view sites that are using the latest technology.</li>
	</ul>

	<h3>Comfort & better experience</h3>

	<p>Have a more comfortable experience with new features, extensions and better customisability.</p>

@endif
