@extends('layouts.app')

@section('body')
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="#">
                <h1>Login In</h1>
                <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form method="POST" action="{{ route('auth.login') }}">
                @csrf
                <h1>Sign in</h1>
                <span>use Js-Adways Erp Account</span>
                <input name='name' placeholder="Account" />
                <input name='password' type="password" placeholder="Password" />
                <button type='button' id='loginIn'>Login In</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>Login with your personal info</p>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your Js-Adways Account Get To Start</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        const loginInButton = document.getElementById('loginIn');
        const container = document.getElementById('container');

            loginInButton.addEventListener('click', () => {
                container.classList.add("right-panel-active");
                run();
            });

        async function sleep(ms = 0) {
            return new Promise(r => setTimeout(r, ms));
        }

        async function run() {
            await sleep(1000);
            $('form').submit();
        }
    </script>
@endsection
