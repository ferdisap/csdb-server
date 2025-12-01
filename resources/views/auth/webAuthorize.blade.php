<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <title>OAuth Authorization</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
  <p id="p1">Authorizing is running...</p>
  <p id="p2"></p>
  <script>
    function init(){
      const search_param = new URLSearchParams(window.location.search)
      const redirect_to = window.location.origin + "/oauth/authorize?" + (new URLSearchParams(window.location.search)).toString();
      // const redirect_to = search_param.get('redirect_to');
      document.getElementById('p2').innerHTML = 'Will be directed to ' + `<a href="${redirect_to}">${redirect_to}</a>`
  
      const access_token = localStorage.getItem('access_token');
      const meta_csrf_token = document.querySelector('meta[name="csrf-token"]');
      const csrf_token = meta_csrf_token.getAttribute('content');
      if (access_token) {
        fetch("/session-regenerate", {
            method: 'POST',
            credentials: "include",
            headers: {
              "X-Requested-With": "XMLHttpRequest",
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Authorization': 'Bearer ' + access_token
            }
          })
          .then(() => {
            // const search_param = new URLSearchParams(window.location.search)
            // const redirect_to = search_param.get('redirect_to');
            window.location.href = redirect_to;
          })
      }
    }
    init();
  </script>
</body>

</html>