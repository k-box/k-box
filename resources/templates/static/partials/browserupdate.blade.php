@if(app()->getLocale() == 'en')

<h1>Please keep your browser up-to-date</h1>

<p>The web browser is the connection between you and the internet. <br/>
Using an older browser exposes you to threats and risks for your personal data.</p>

<p>On K-Link we promote the usage of modern technologies, therefore to be able to use all the 
features available a modern and updated browser is needed.</p>

<p>You can keep your browser updated following your Operating System upgrade notices. <br/>
If you are not able to upgrade your current browser, or your IT department won't do that, 
you can switch to an alternative browser.</p>

<div class="outdated__browsers">

<a class="outdated__browser" href="https://support.microsoft.com/en-us/help/17621/internet-explorer-downloads"><img src="{{ url('/') }}/images/internet-explorer_9-11_64x64.png" /><br/>Internet Explorer (10 or 11)</a>

<a class="outdated__browser" href="https://www.mozilla.com/firefox/"><img src="{{ url('/') }}/images/firefox_64x64.png" /><br/>Firefox</a>

<a class="outdated__browser" href="https://www.google.com/chrome/browser/desktop/"><img src="{{ url('/') }}/images/chrome_64x64.png" /><br/>Chrome</a>

<a class="outdated__browser" href="http://www.opera.com/"><img src="{{ url('/') }}/images/opera_64x64.png" /><br/>Opera</a>

</div>

<h2>Why do I need an up-to-date browser?</h2>

<h3>Security</h3>

<p>Newer browsers protect you better against viruses, scams and other threats. Outdated browsers have security holes which are fixed in updates.</p>

<h3>Compatibility & new Technology</h3>

<ul>
	<li>You can use the latest K-Link enhancements.</li>
	<li>You can view sites that are using the latest technology.</li>
</ul>

<h3>Comfort & better experience</h3>

<p>Have a more comfortable experience with new features, extensions and better customisability.</p>

@else 


		
<h1>Пожалуйста обновите свой браузер.</h1>

<p>Браузер - это программа, которая дает пользователю возможность просмотривать Интернет сайты.<br/>
Использование устаревших версий браузера подвергает Вашу личную информацию различным рискам и угрозам.</p>

<p>В K-Link используются самые современные технологии, и чтобы полноценно их использовать нужен современный и обновленный браузер.</p>

<p>Вы можете обновлять Ваш браузер при уведомлениях Вашей операционной системы.<br/>
Если Вы или отдел информационных технологий не может обновить браузер, используйте альтернативный браузер.</p>

<div class="outdated__browsers">

<a class="outdated__browser" href="https://support.microsoft.com/en-us/help/17621/internet-explorer-downloads"><img src="{{ url('/') }}/images/internet-explorer_9-11_64x64.png" /><br/>Internet Explorer (10 или 11)</a>

<a class="outdated__browser" href="https://www.mozilla.com/firefox/"><img src="{{ url('/') }}/images/firefox_64x64.png" /><br/>Firefox</a>

<a class="outdated__browser" href="https://www.google.com/chrome/browser/desktop/"><img src="{{ url('/') }}/images/chrome_64x64.png" /><br/>Chrome</a>

<a class="outdated__browser" href="http://www.opera.com/"><img src="{{ url('/') }}/images/opera_64x64.png" /><br/>Opera</a>

</div>

<h2>Почему я должен обновлять браузер?</h2>

<h3>Безопасность</h3>

<p>Обновленные браузеры лучше защищают от вирусов, мошеничества и других угроз. В старых версиях могут быть недостатки в системе безопасности, которые исправляются в обновленных версиях.</p>

<h3>Совместимость и новые технологии</h3>

<ul>
	<li>Вы будете использовать улучшенный K-Link.</li>
	<li>Вы сможете посещать сайты, использующие последние технологии.</li>
</ul>

<h3>Улучшенное и удобное пользование</h3>

<p>Используйте более удобный сервис, с новыми возможностям, расширениям и улучшенным настойкам.</p>


@endif
