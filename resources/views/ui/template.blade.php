@extends('layouts.app')
{{-- @extends('layouts.navbar') --}}

@section('content')



	<!-- topics -->
	<section class="section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-12 text-center">
					<h2 class="section-title">Find your answer by subject</h2>
				</div>
				<!-- topic-item -->
				<div class="col-lg-4 col-sm-6 mb-4">
					<a href="single.html" class="px-4 py-5 bg-white shadow text-center d-block match-height">
						<i class="ti-panel icon text-primary d-block mb-4"></i>
						<h3 class="mb-3 mt-0">Installation</h3>
						<p class="mb-0">Lorem ipsum dolor sit amet ipsum dolor sit amet ipsum dolor sit amet</p>
					</a>
				</div>
				<div class="col-lg-4 col-sm-6 mb-4">
					<a href="single.html" class="px-4 py-5 bg-white shadow text-center d-block match-height">
						<i class="ti-credit-card icon text-primary d-block mb-4"></i>
						<h3 class="mb-3 mt-0">Billing &amp; Pricing</h3>
						<p class="mb-0">Lorem ipsum dolor sit amet ipsum dolor sit amet ipsum dolor sit amet</p>
					</a>
				</div>
				<div class="col-lg-4 col-sm-6 mb-4">
					<a href="single.html" class="px-4 py-5 bg-white shadow text-center d-block match-height">
						<i class="ti-package icon text-primary d-block mb-4"></i>
						<h3 class="mb-3 mt-0">Features</h3>
						<p class="mb-0">Lorem ipsum dolor sit amet ipsum dolor sit amet ipsum dolor sit amet</p>
					</a>
				</div>
			</div>
		</div>
	</section>
	<!-- /topics -->

	<!-- call to action -->
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="section px-3 bg-white shadow text-center">
						<h2 class="mb-4">Didn&rsquo;t find an answer to your question?</h2>
						<p class="mb-4">Musce libero nunc, dignissim quis turpis quis, semper vehicula dolor. Suspendisse tincidunt
							consequat quam.</p>
						<a href="contact.html" class="btn btn-primary">contact us</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- /call to action -->

	<!-- footer -->
	<footer class="section pb-4">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-8 text-md-left text-center">
					<p class="mb-md-0 mb-4">Copyright © 2020 Designed and Developed by <a
							href="https://themefisher.com/">themefisher</a></p>
				</div>
				<div class="col-md-4 text-md-right text-center">
					<ul class="list-inline">
						<li class="list-inline-item"><a class="text-color d-inline-block p-2" href="#"><i
									class="ti-facebook"></i></a></li>
						<li class="list-inline-item"><a class="text-color d-inline-block p-2" href="#"><i
									class="ti-twitter-alt"></i></a></li>
						<li class="list-inline-item"><a class="text-color d-inline-block p-2" href="#"><i class="ti-github"></i></a>
						</li>
						<li class="list-inline-item"><a class="text-color d-inline-block p-2" href="#"><i
									class="ti-linkedin"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
	</footer>
