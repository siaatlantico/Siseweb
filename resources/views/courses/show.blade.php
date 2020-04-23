@extends('plantilla')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/estudiantes.css') }}">
@endsection

@section('content')

@if (session()->get('danger'))
      <div class="alert alert-success">
          {{ session()->get('danger') }}
      </div>
      @endif
      
    <div class="header-container">
        <div class="title">
            <h1>
                <i class="far fa-file-alt"></i>
                Ver Curso
            </h1>
        </div>


    </div>
    @if ($errors->any())
    <div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    </div><br />
@endif
<div class="row col-lg-12" style="border-radius: 1rem;background-color: white;box-shadow: 0 0 2px 1px #c3c3c3; margin-left: 3px; padding: 25px 15px;">
    
    <div class="col-md-12 col-lg-6 form-left">
                <div class="form-group row" style="margin-top: 1rem;">
                    <label for="col-sm-4 col-form-label">Nombre:</label>
                    <div class="col-sm-8 input-group">
                        <input type="text" class="form-control" name="course">
                    </div>
                </div>
            </div>
            
            <div class="col-md-12 col-lg-6 form-left">
                <div class="form-group row" style="margin-top: 1rem;">
                    <label for="col-sm-4 col-form-label">Abreviatura:</label>
                    <div class="col-sm-8 input-group ">
                        <input type="text" class="form-control" name="variation">
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 form-left">
                <div class="form-group row" style="margin-top: 1rem;">
                    <label for="col-sm-4 col-form-label">Jornada:</label>
                    <div class="col-sm-8 input-group ">
                        <input type="text" class="form-control" name="working_day">
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 form-left">
                <div class="form-group row" style="margin-top: 1rem;">
                    <label for="col-sm-4 col-form-label">Director de grupo:</label>
                    <div class="col-sm-8 input-group ">
                        <input type="text" class="form-control" name="teacher">
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-6 form-left mt-4">
                <a href="{{ route('cursos.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
@endsection
