@extends('layouts.layout')

@section('content')
<div class="page-header" id="banner">
  	<div class="row">
      	<div class="col-lg-8 col-md-7 col-sm-6">
      		<h1>Frequently Asked Questions</h1>
      	</div>
  	</div>
</div>



	<div class="col-sm-8 blog-main">

		<div class="well"> 
             <form class="form">
              <div class="input-group text-center">
              <input type="text" class="form-control" placeholder="Type your search term here...">
                <span class="input-group-btn"><button class="btn btn-primary" type="button">Go</button></span>
              </div>
            </form>
        </div>

        <div class="row">
	        <div class="col-lg-4">
	          	<h2>Safari bug warning!</h2>
	          	<ul>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          	</ul>
		        <p><a href="#" role="button">View More »</a></p>
	        </div>

	        <div class="col-lg-4">
	          	<h2>Safari bug warning!</h2>
	          	<ul>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          	</ul>
		        <p><a href="#" role="button">View More »</a></p>
	        </div>

	        <div class="col-lg-4">
	          	<h2>Safari bug warning!</h2>
	          	<ul>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          	</ul>
		        <p><a href="#" role="button">View More »</a></p>
	        </div>
     	</div>

     	<div class="row">
	        <div class="col-lg-4">
	          	<h2>Safari bug warning!</h2>
	          	<ul>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          	</ul>
		        <p><a href="#" role="button">View More »</a></p>
	        </div>

	        <div class="col-lg-4">
	          	<h2>Safari bug warning!</h2>
	          	<ul>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          	</ul>
		        <p><a href="#" role="button">View More »</a></p>
	        </div>

	        <div class="col-lg-4">
	          	<h2>Safari bug warning!</h2>
	          	<ul>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          		<li><a href="">Topic 1</a></li>
	          	</ul>
		        <p><a href="#" role="button">View More »</a></p>
	        </div>
     	</div>

        <!-- search return -->
		<!-- <div class="list-group"> 
			<a href="#" class="list-group-item"> 
				<h4 class="list-group-item-heading">List group item heading</h4> 
				<p class="list-group-item-text">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p> 
			</a> 
			<a href="#" class="list-group-item"> 
				<h4 class="list-group-item-heading">List group item heading</h4> 
				<p class="list-group-item-text">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p> 
			</a> 
		</div> -->

		<!-- detailed -->
		<!-- <div class="blog-post">
			<h2 class="blog-post-title">Sample blog post</h2>
			<p class="blog-post-meta">January 1, 2014 by <a href="#">Mark</a></p>

			<p>This blog post shows a few different types of content that's supported and styled with Bootstrap. Basic typography, images, and code are all supported.</p>
			<hr>
			
		</div>

		<nav>
			<ul class="pager">
				<li><a href="#">Previous</a></li>
				<li><a href="#">Next</a></li>
			</ul>
		</nav> -->
		

	</div><!-- /.blog-main -->

	<div class="col-sm-3 col-sm-offset-1 blog-sidebar">
		<div class="sidebar-module sidebar-module-inset">
			<h4>About</h4>
			<p>Etiam porta <em>sem malesuada magna</em> mollis euismod. Cras mattis consectetur purus sit amet fermentum. Aenean lacinia bibendum nulla sed consectetur.</p>
		</div>
		<div class="sidebar-module">
			<h4>Archives</h4>
			<ol class="list-unstyled">
				<li><a href="#">March 2014</a></li>
				<li><a href="#">February 2014</a></li>
				<li><a href="#">January 2014</a></li>
				<li><a href="#">December 2013</a></li>
				<li><a href="#">November 2013</a></li>
				<li><a href="#">October 2013</a></li>
				<li><a href="#">September 2013</a></li>
				<li><a href="#">August 2013</a></li>
				<li><a href="#">July 2013</a></li>
				<li><a href="#">June 2013</a></li>
				<li><a href="#">May 2013</a></li>
				<li><a href="#">April 2013</a></li>
			</ol>
		</div>
		<div class="sidebar-module">
			<h4>Elsewhere</h4>
			<ol class="list-unstyled">
				<li><a href="#">GitHub</a></li>
				<li><a href="#">Twitter</a></li>
				<li><a href="#">Facebook</a></li>
			</ol>
		</div>
	</div><!-- /.blog-sidebar -->

</div>

@stop