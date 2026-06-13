<x-app-layout layout="landing">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="{{ asset('images/landing-pages/images/home-1/aboutus.webp') }}" alt=""
                        class="img-fluid ">
                </div>
                <div class="col-md-6">
                    <p class="mb-2 text-secondary text-uppercase">
                        about us
                    </p>
                    <h2 class="text-secondary mb-4">What they say <br> <span class="text-primary">About Us</span></h2>
                    <p class="mb-5">It is a long established fact that a reader will be distracted by the readable
                        content
                        of a page when looking at its layout. </p>
                    <a hrer="javascript" class="btn btn-primary">Get Started</a>
                </div>
            </div>
        </div>
    <div class="section-padding page-bg">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <p class="mb-2 text-secondary text-uppercase">
                        Download now
                    </p>
                    <h2 class="text-secondary mb-4 fast-afrd-txt">Fast, easy, and <br> <span
                            class="text-primary">Affordable</span></h2>
                    <p class="mb-5">It is a long established fact that a reader will be distracted by the readable
                        content
                        of a page when looking at its layout. </p>
                    <div class="d-flex align-items-center store-btn flex-wrap">
                        <img src="{{ asset('images/landing-pages/images/home-1/playstore.webp') }}" alt=""
                            class="img-fluid mb-3 mb-md-0">
                        <img src="{{ asset('images/landing-pages/images/home-1/appstore.webp') }}" alt=""
                            class="img-fluid ms-0 ms-md-3">
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <img src="{{ asset('images/landing-pages/images/home-1/dwn-1.webp') }}" alt=""
                                class="img-fluid">
                        </div>
                        <div class="col">
                            <img src="{{ asset('images/landing-pages/images/home-1/dwn-2.webp') }}" alt=""
                                class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="section-card-padding page-bg">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12 text-center">
                    <p class="mb-2 text-uppercase text-secondary">
                        Blog
                    </p>
                    <h2 class="text-secondary mb-4">All the <span class="text-primary">Support you Need</span></h2>
                </div>
                <div class="overflow-hidden slider-circle-btn mt-5" id="app-slider">
                    <ul class="p-0 m-0 swiper-wrapper list-inline">
                      
                    </ul>
                    <div class="swiper-button swiper-button-next"></div>
                    <div class="swiper-button swiper-button-prev"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="section-padding bg-white">
        <div class="container">
            <div class="row align-items-center text-center row-cols-2 row-cols-sm-2 row-cols-md-2 row-cols-lg-4">
                <div class="col mb-lg-0 mb-4">
                    <x-landing-pages.widgets.counter couterVlue="3" counterTitle="Best Designer Award" />
                </div>
                <div class="col mb-lg-0 mb-4">
                    <x-landing-pages.widgets.counter couterVlue="50+" counterTitle="Loyal Clients" />
                </div>
                <div class="col">
                    <x-landing-pages.widgets.counter couterVlue="158+" counterTitle="Nominee Awards" />
                </div>
                <div class="col">
                    <x-landing-pages.widgets.counter couterVlue="92+" counterTitle="CSS Designs" />
                </div>
            </div>
        </div>
    </div>
    <div class="inner-box bg-secondary">
        <div class="container">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-5 justify-content-center ">
                <div class="col mb-md-0 mb-5 d-flex justify-content-center">
                    <x-landing-pages.widgets.client clientImage="07.webp" />
                </div>
                <div class="col mb-md-0 mb-5 d-flex justify-content-center">
                    <x-landing-pages.widgets.client clientImage="08.webp" />
                </div>
                <div class="col mb-md-0 mb-5 d-flex justify-content-center">
                    <x-landing-pages.widgets.client clientImage="09.webp" />
                </div>
                <div class="col mb-md-0 mb-5 d-flex justify-content-center">
                    <x-landing-pages.widgets.client clientImage="10.webp" />
                </div>
                <div class="col d-flex justify-content-center">
                    <x-landing-pages.widgets.client clientImage="11.webp" />
                </div>
            </div>
        </div>
    </div>
    <div class="section-padding">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-lg-12">
                    <p class="mb-4 text-uppercase text-secondary">Testimony</p>
                    <h2 class="text-secondary customer-txt">What our <span class="text-primary">Customer’s <br> are
                            saying</span></h2>
                </div>
                <div class="overflow-hidden slider-circle-btn" id="testimonial-slider">
                    <ul class="p-0 m-0 swiper-wrapper list-inline">
                        <li class="swiper-slide">
                            <x-landing-pages.widgets.testiominal
                                testText="Torquatos nec eu, detr
                        periculis ex, nihil expetendis in mei. Mei an pericula euripidis.hinc partem ei est. Eos ei nisl
                        graecis, vix aperiri consequat an. Eius lorem tincidunt vix at, vel pertinax sensibus id, error
                        epicurei mea. Mea facilisis urbanitas.Torquatos nec eu, detr periculis ex, nihil expetendis in
                        mei."
                                testOwner="Elsa Schmidt" testSubtitle="Spa" />
                        </li>
                        <li class="swiper-slide">
                            <x-landing-pages.widgets.testiominal
                                testText="Torquatos nec eu, detr
                        periculis ex, nihil expetendis in mei. Mei an pericula euripidis.hinc partem ei est. Eos ei nisl
                        graecis, vix aperiri consequat an. Eius lorem tincidunt vix at, vel pertinax sensibus id, error
                        epicurei mea. Mea facilisis urbanitas.Torquatos nec eu, detr periculis ex, nihil expetendis in
                        mei."
                                testOwner="Elsa Schmidt" testSubtitle="Spa" />
                        </li>
                        <li class="swiper-slide">
                            <x-landing-pages.widgets.testiominal
                                testText="Torquatos nec eu, detr
                        periculis ex, nihil expetendis in mei. Mei an pericula euripidis.hinc partem ei est. Eos ei nisl
                        graecis, vix aperiri consequat an. Eius lorem tincidunt vix at, vel pertinax sensibus id, error
                        epicurei mea. Mea facilisis urbanitas.Torquatos nec eu, detr periculis ex, nihil expetendis in
                        mei."
                                testOwner="Elsa Schmidt" testSubtitle="Spa" />
                        </li>
                        <li class="swiper-slide">
                            <x-landing-pages.widgets.testiominal
                                testText="Torquatos nec eu, detr
                        periculis ex, nihil expetendis in mei. Mei an pericula euripidis.hinc partem ei est. Eos ei nisl
                        graecis, vix aperiri consequat an. Eius lorem tincidunt vix at, vel pertinax sensibus id, error
                        epicurei mea. Mea facilisis urbanitas.Torquatos nec eu, detr periculis ex, nihil expetendis in
                        mei."
                                testOwner="Elsa Schmidt" testSubtitle="Spa" />
                        </li>
                        <li class="swiper-slide">
                            <x-landing-pages.widgets.testiominal
                                testText="Torquatos nec eu, detr
                        periculis ex, nihil expetendis in mei. Mei an pericula euripidis.hinc partem ei est. Eos ei nisl
                        graecis, vix aperiri consequat an. Eius lorem tincidunt vix at, vel pertinax sensibus id, error
                        epicurei mea. Mea facilisis urbanitas.Torquatos nec eu, detr periculis ex, nihil expetendis in
                        mei."
                                testOwner="Elsa Schmidt" testSubtitle="Spa" />
                        </li>
                    </ul>
                    <div class="swiper-button swiper-button-next"></div>
                    <div class="swiper-button swiper-button-prev"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
