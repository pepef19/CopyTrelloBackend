<style type="text/css">
    h2 {
        text-align: center;
    }

    .text-align-center {
        text-align: center;
    }

</style>

@component('mail::message')

    <h2 class="text-align-center">Dear {{$data}}, welcome to Trello! <br/> Please press the button below to access your Home Page :)</h2>

@component('mail::button', ['url' => 'http://localhost:3000/home'])

    Visit Trello

@endcomponent

@endcomponent

