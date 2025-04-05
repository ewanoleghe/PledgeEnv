<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta itemprop="name" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="description" content="Pledge Environmental promotes environmental safety and public health through expert lead evaluation and assessment service to residential, governmental, and commercial clients throughout the U.S." />
  {{-- font-awesome  --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    @vite('resources/js/app.js')
    @inertiaHead
    @routes
  </head>
  <body class="font-Montserrat bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white">
    @inertia
    {{-- Stripe JS --}}
    <script src="https://js.stripe.com/v3/"></script>
  </body>
</html>