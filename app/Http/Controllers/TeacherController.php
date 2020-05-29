<?php

namespace App\Http\Controllers;

use App\City;
use App\Teacher;
use App\User;
use App\Type_document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;


class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $teachers = Teacher::all();
        return view('teachers.index',  compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $type_document =  Type_document::all();
        $city = City::all();
        return view('teachers.create', compact('type_document', 'city'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'primer_nombre' => 'required',
            'segundo_nombre' => 'required',
            'apellidos' => 'required',
            'municipio' => 'required',
            'tipo_de_documento' => 'required',
            'profesion' => 'required',
            'numero_de_documento' => 'required|min:10|max:10|unique:teachers,number_document',

        ]);

        if ($validator->fails()) {
            return back()->withToastError($validator->messages()->all()[0])->withInput();
        }

        $TeacherStore = new Teacher([
            'first_name'       => $request->get('primer_nombre'),
            'second_name'      => $request->get('segundo_nombre'),
            'last_name'        => $request->get('apellidos'),
            'city_id'          => $request->get('municipio'),
            'profession'        => $request->get('profesion'),
            'type_document_id' => $request->get('tipo_de_documento'),
            'number_document'  => $request->get('numero_de_documento'),
            'expedition_date'  => $request->get('fecha_de_expedicion'),
            'birth_date'       => $request->get('fecha_de_nacimiento')
        ]);

        if (Teacher::where('number_document', $request->get('numero_de_documento'))->exists()) {

            return back()->withToastError('El Estudiante ' . $request->get('primer_nombre') . ' Ya Esta En Los Registros!');
        } else {



        if ($TeacherStore->save()) {

            $RegisterDateUser =  Teacher::all()->last();

            //generamos el usuario para este profesor

            User::create([

                'nombre' => $RegisterDateUser->first_name,
                'apellidos' => $RegisterDateUser->second_name,
                'cargo' => 'Profesor',
                'teacher_id' => $RegisterDateUser->id,
                'student_id' => null,
                'cedula'    => $RegisterDateUser->number_document,
                'cedula_verified_at' => now(),
                'password' => bcrypt($RegisterDateUser->number_document),
                'type_user' => 'Teacher',
                'remember_token' => Str::random(10)
            ]);

            Teacher::where('is_user', false)
                ->where('id', $RegisterDateUser->id)
                ->update(['is_user' => true]);

                return back()->withToastSuccess('Registro y generacion de usuario realizado correctamente!');

        }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        $TeacherShow = Teacher::findOrfail($id);
        return view('teachers.show', compact('TeacherShow'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $TeacherEdit = Teacher::find($id);
        $TeacherCity = City::all();
        $TeacherTypeDocument = Type_document::all();
        return view('teachers.edit', compact('TeacherEdit', 'TeacherCity', 'TeacherTypeDocument'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'primer_nombre' => 'required',
            'segundo_nombre' => 'required',
            'apellidos' => 'required',
            'municipio' => 'required',
            'tipo_de_documento' => 'required',
            'profesion' => 'required',
            'numero_de_documento' => 'required|min:10|max:10',

        ]);

        if ($validator->fails()) {
            return back()->withToastError($validator->messages()->all()[0])->withInput();
        }

        $TeacherUpdate = Teacher::find($id);
        $TeacherUpdate->first_name = $request->get('primer_nombre');
        $TeacherUpdate->second_name      =  $request->get('segundo_nombre');
        $TeacherUpdate->last_name      =  $request->get('apellidos');
        $TeacherUpdate->city_id          =  $request->get('municipio');
        $TeacherUpdate->type_document_id =  $request->get('tipo_de_documento');
        $TeacherUpdate->profession       =  $request->get('profesion');
        $TeacherUpdate->number_document  =  $request->get('numero_de_documento');
        $TeacherUpdate->expedition_date  =  $request->get('fecha_de_expedicion');
        $TeacherUpdate->birth_date      =  $request->get('fecha_de_nacimiento');
        $TeacherUpdate->save();

        return redirect('/profesores')->withToastSuccess('Registro Actualizado Correcatamente!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // verificamos que el profesor no tenga cursos o cargas academica
        // para asi no se pueda eliminar el dato ya que otras tabla dependen de ese dato.


            $asignacion_curso = DB::table('courses')->select('course')->where('teacher_id', '=', $id)->get();

            $asignacion_academica = DB::table('academic_assignments')->select('course_id')->where('teacher_id', '=', $id)->get();


        if (count($asignacion_curso) > 0 || count($asignacion_academica) > 0) {


           alert()->error('Siseweb','Este profesor no puede ser eliminado del sistema,
           debido a que tiene cursos o cargas academicas asignadas.');

           return redirect('/profesores');

        }else{

            $Teacher = Teacher::find($id);

            $Teacher->delete();

            return redirect('/profesores')->withToastSuccess('Registro Eliminado Correcatamente!');

        }

    }
}
