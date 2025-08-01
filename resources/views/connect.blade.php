<html lang="en">
<head>
    <title>Docusign Integration Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            CodeHunger Private Limited
        </div>
        <div class="card-body">
            <h5 class="card-title">Docusign Tutorial</h5>
            <p class="card-text">Click the button below to connect your appication with docusign</p>
            @if ($message = Session::get('success'))
                <a href="{{route('docusign.sign')}}" class="btn btn-primary">Click to sign document</a>
            @else
                <a href="{{route('connect.docusign')}}" class="btn btn-primary">Connect Docusign</a>
            @endif

        </div>
    </div>
</div>
</body>