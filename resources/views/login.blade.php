<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Diran</title>

    <!-- META-Content-DATA -->
    <meta http-equiv="content-type" content="text/html; charset=utf-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="" >
    <meta name="keywords" content="" >
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/login/css/base.css') !!}">
    <style>

        @media only screen and (max-width: 767px) {
            .landing-section .service-list{
                padding: 30% 30px 0;
                width: auto;
            }
            .landing-section .service-list .list-content{
                display: block;
            }
            .landing-section .service-list .list-content .service-block {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid landing-section inner-landing-section">
    <div class="animated-bg">
        <img src="{!! asset('assets/login/images/insulator2.gif') !!}">
    </div>
    <div class="top-navbar">
        <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link" href="http://54.255.142.93/enterprise/public/">Enterprise</a></li>
            <li class="nav-item"><a class="nav-link active" href="http://54.255.142.93/diran/public/">Insulator</a></li>
            <li class="nav-item"><a class="nav-link" href="http://54.255.142.93/pole/public/">Pole</a></li>
        </ul>
    </div>
    <div class="login-section">
        <div class="login-block">
            <div class="service-logo">
                <a href="#">
                    <?php if(isset($company_info->logo)) {?>
                    <img src="{{url('uploads/company_logo').'/'. $company_info->logo}}" width="150px" onerror="this.src='img/1200px-Channel-i.png';" >
                    <?php }else{ ?>
                    <img src="{!! asset('assets/app/media/img/logos/iventure-logo.png') !!}" width="300px">
                    <?php } ?>
                </a>
            </div>
            <h5 style="text-align: center">Log In To @if(isset($company_info->name))
                    {{$company_info->name}}
                @else
                    IVL ERP
                @endif
            </h5>
            {!! Form::open(['url' => 'login','class'=>'m-login__form m-form']) !!}
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" class="form-control">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" class="form-control">
                </div>
                <div class="form-group">
                    <div class="remember">
                        <input type="checkbox"><span>Remember</span>
                    </div>
                    {{--<div class="forgot">
                        <a href="">Forgot Password ?</a>
                    </div>--}}
                </div>
                <div class="form-group">
                    <button type="submit" class="login-btn">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Log In&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


</body>
</html>
