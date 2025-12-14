<footer class="bg-gray-900 text-gray-300 mt-8 mb-20">
    <div class="max-w-screen-md mx-auto px-4 py-8">
        <div class="flex flex-col items-center justify-center space-y-4">
            <!-- لوگوی اینماد -->
            <div class="enamad-logo">
                <a referrerpolicy='origin' target='_blank' href='https://trustseal.enamad.ir/?id=668272&Code=OJG9rk1Il7Gg1QVCca78Alvk4RRDPkse'>
                    <img referrerpolicy='origin' src='https://trustseal.enamad.ir/logo.aspx?id=668272&Code=OJG9rk1Il7Gg1QVCca78Alvk4RRDPkse' alt='اینماد' style='cursor:pointer' code='OJG9rk1Il7Gg1QVCca78Alvk4RRDPkse'>
                </a>
            </div>
            
            <!-- Copyright -->
            <div class="text-center text-sm text-gray-400">
                <p>© {{ date('Y') }} {{ config('app.name', 'آزمون کده') }} - تمامی حقوق محفوظ است.</p>
            </div>
        </div>
    </div>
</footer>

<style>
    .enamad-logo img {
        max-width: 100px;
        height: auto;
        display: block;
    }
    
    .enamad-logo a {
        display: inline-block;
        transition: opacity 0.2s;
    }
    
    .enamad-logo a:hover {
        opacity: 0.8;
    }
</style>
