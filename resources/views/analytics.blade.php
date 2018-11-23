
@auth
  @if(analytics_token() !== false && \KBox\Consent::isGiven(\KBox\Consents::STATISTIC))

  <!-- Piwik -->
  <script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
      var u="//analytics.klink.asia/";
      _paq.push(['setTrackerUrl', u+'piwik.php']);
      _paq.push(['setSiteId', '{{ analytics_token() }}']);
      var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
      g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
    })();
  </script>
  <noscript><p><img src="//analytics.klink.asia/piwik.php?idsite={{ analytics_token() }}" style="border:0;" alt="" /></p></noscript>
  <!-- End Piwik Code -->
  @endif
@endauth