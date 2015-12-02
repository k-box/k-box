<html>
	<head>


		<title>@yield('title')</title>


		<style>


			body {
				background: #283593;

				font-family: 'Nunito', sans-serif;

				color: #fff;

				padding: 40px;
			}

			.title {
				font-size: 40px;
				margin-top: 28px;
			}

			.dms_tag {
				font-size: 18px;
				margin: 14px 0;
			}

			.logo {
				background: transparent url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAeFJREFUeNrMmAF1hDAMQMveBOBgSEAC52AOxhwggSk4CcwBc3AS2BSwKeAc9FKWstBx0AYKzXt5cO+S9r+UtkmEAJFSFqCd/JMWNBeeBMaOcc4LmbMBPYMmpnGBBh06UKfKA1w6EYyG/O5GwUEDJakxiHbKPMGNooVRLQlorv9QcrmzDL7g8gU7HclEA56FR7GFI/Y6kqUYXgKBQ59kWFmfgBw44qv8Om+Aa+DI3mgffEUOHmrjxaCvURS9O/rr3f29eQTXRg7HqAf/LQE3gsv18tK1LgOD64aLYwtAb3BbADLPucy45qbh1gIy4Sq0rxfh1gCuhOvQfx6OC7gbHAdwVzhXQLzE94NjAOrJil3gGIC/GQYPLnOGYwBOZt9LcEYS+uyyIV2zmStosgQHjxxtT5DJfBJfJS+cvMs2gvXcNzgVOaPGaZyrRUfAlFRdhTF5tfSNsSAZ52Aux2LWtKlF0W4PybxJEoxYSyDVJogdOgt2kL6rutWQRwFaQx4JaAV5NOAiZAiAs5ChAN6DfBQBCVyLVwA7YdGvztsfQeuDUIQ0j1oxKpLDguw7vSqb+VAZis+e9BYdgf+94eM3S58cRzoBgEc1dJSEUDnc10F8T6AZ5p1vZiRrGY70bembAAMAMDjWWiaHzWEAAAAASUVORK5CYII=') no-repeat center center;
				display: inline-block;
				width: 48px;
				height: 48px;
			}

		</style>
		<link href='//fonts.googleapis.com/css?family=Nunito:300,700' rel='stylesheet' type='text/css'>
		
	</head>
	<body>
		<div class="container">
			<div class="content">

				<div class="logo">&nbsp;</div>

				<div class="dms_tag">K-Link DMS</div>

				<div class="title">@yield('content')</div>
			</div>
		</div>
	</body>
</html>
