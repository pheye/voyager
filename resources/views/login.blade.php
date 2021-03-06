<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="none" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="admin login">
    <title>Admin. - {{ Voyager::setting("title") }}</title>
    <link rel="stylesheet" href="{{ config('voyager.assets_path') }}/lib/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ config('voyager.assets_path') }}/css/login.css">
    <style>
        body {
            background-image:url('{{ Voyager::image( Voyager::setting("admin_bg_image"), config('voyager.assets_path') . "/images/bg.jpg" ) }}');
        }
        .login-sidebar:after, .gradient-button {
            background: linear-gradient(-135deg, {{config('voyager.login.gradient_a','#3f51b5')}}, {{config('voyager.login.gradient_b','#e91e63')}});
            background: -webkit-linear-gradient(-135deg, {{config('voyager.login.gradient_a','#3f51b5')}}, {{config('voyager.login.gradient_b','#e91e63')}});
        }
    </style>

    <!-- <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
</head>
<body>
<!-- Designed with ♥ by Frondor -->
<div class="container-fluid">
    <div class="row">
        <div class="faded-bg"></div>
        <div class="hidden-xs col-sm-8 col-md-9">
            <div class="clearfix">
                <div class="hidden-xs" style="margin-top:200px"></div>
                <div class="col-sm-12 col-md-10 col-md-offset-2">
                    <?php $admin_logo_img = Voyager::setting('logo', ''); ?>
                    @if($admin_logo_img == '')
                    <img class="img-responsive pull-left logo hidden-xs" src="{{ config('voyager.assets_path') }}/images/logo-icon-light.png" alt="Logo Icon">
                    @else
                    <img class="img-responsive pull-left logo hidden-xs" src="{{ Voyager::image($admin_logo_img) }}" alt="Logo Icon">
                    @endif
                    <div class="copy">
                        <h1>{{ Voyager::setting('admin_title', 'Voyager') }}</h1>
                        <p>{{ Voyager::setting('admin_description', 'Welcome to Voyager. The Missing Admin for Laravel') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-3 login-sidebar">

            <div class="logo-container">    
                <?php $admin_logo_img = Voyager::setting('logo', ''); ?>
                @if($admin_logo_img == '')
                    <img class="img-responsive logo" src="{{ config('voyager.assets_path') }}/images/logo-icon-light.png" alt="Logo Icon">
                @else
                    <img class="img-responsive logo" src="{{ Voyager::image($admin_logo_img) }}" alt="Logo Icon">
                @endif
            </div>

            <form action="{{ route('voyager.login', [], false) }}" method="POST">
            {{ csrf_field() }}
            <div class="group">      
              <input type="text" name="email" value="{{ old('email') }}" required>
              <span class="highlight"></span>
              <span class="bar"></span>
              <label><i class="glyphicon glyphicon-user"></i><span class="span-input"> E-mail</span></label>
            </div>

            <div class="group">      
              <input type="password" name="password" required>
              <span class="highlight"></span>
              <span class="bar"></span>
              <label><i class="glyphicon glyphicon-lock"></i><span class="span-input"> Password</span></label>
            </div>

            <button type="submit" class="btn btn-block gradient-button">
                <span class="signingin hidden"><span class="glyphicon glyphicon-refresh"></span> Loggin in...</span>
                <span class="signin">Login</span>
            </button>
           
          </form>

          @if(!$errors->isEmpty())
          <div class="alert alert-black">
            <ul class="list-unstyled">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach                
            </ul>
          </div>            
          @endif
            
        </div> <!-- .login-sidebar -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->
<script>
    var btn = document.querySelector('button[type="submit"]');
    var form = document.forms[0];
    btn.addEventListener('click', function(ev){
        if (form.checkValidity()) {
            btn.querySelector('.signingin').className = 'signingin';
            btn.querySelector('.signin').className = 'signin hidden';
        } else {
            ev.preventDefault();
        }
    });
</script>
</body>
</html>
