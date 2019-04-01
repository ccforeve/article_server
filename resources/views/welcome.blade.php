<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }
            ul li {
                float: left;
                list-style: none;
                padding-left: 10px;
            }
        </style>
    </head>
    <body>
        <ul>
            <li>用户列表</li>
        </ul>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <input type="text" name="message">
                <button name="sub">发送</button>
            </div>
        </div>
    </body>
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
    <script>
        let ul = $('ul');
      var wsServer = 'ws://101.200.51.186:5200';
      var websocket = new WebSocket(wsServer);
      websocket.onopen = function (evt) {
        console.log("登录成功.");
      };

      websocket.onclose = function (evt) {
        console.log("Disconnected");
      };

      websocket.onmessage = function (evt) {
        evt.data.split('|').forEach(function (value) {
          $('ul').append("<li>"+ value +"</li>")
        })
      };

      websocket.onerror = function (evt, e) {
        console.log('Error occured: ' + e);
      };

      $('button').click(function () {
        let message = $('input[name=message]').val()
        websocket.send(message)
      })
    </script>
</html>
