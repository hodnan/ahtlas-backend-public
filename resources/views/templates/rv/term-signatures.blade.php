@extends('templates.rv.model')
@section('body')
<div class="container-fluid">

    <!-- {{-- Headeer --}} -->
    <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(public_path('assets/img/logo-placeholder.svg'))) }}"
    style="position: absolute; top:-40px; left:-5px; height: 50px; margin: 10px;">

    <div style="text-align: center; width: 100%;">
        <h2 style="display: inline-block; margin-top: 0; margin-bottom: 0; width: 100%;">Termos por colaborador
        </h2>
    </div>

    <br />
   
    @if($employee)
        <div style="padding-left: 10px; height: 70px;">
            <div style="float:left;">             
                <img height="70px" src="{{ $avatarBase64 }}">             
                </div>
            <div style="float:left; text-align: left; padding-left: 10px; font-size: 14px;">                
                <b>Nome:</b> {{$employee['name']}} <br>
                <b>Matrícula:</b> {{  preg_replace('/\D/', '', $employee['username']); }}  <br>
                <b>Admissão: </b>{{$employee['admission']}} <br>
            </div>
        </div>       
    @endif
    
    <br />  

    <table border="1" cellspacing='0' style="margin-left: auto;    margin-right: auto;   width: 100%;">
        <thead>
            <tr class="table_header basket">
                <td align="center">Mês</td>
                <td align="center">Termo</td>
                <td align="center">Disponível em </td>
                <td align="center">Assinado em</td>
                <td align="center">Status</td>                
            </tr>
           
        </thead>
       
        <tbody>
            @foreach ( $terms as $key => $value )
            <tr>
                <td align="center" style="padding-left: 5px"> {{$value['month_ref']}} </td>
                <td  style="padding-left: 5px"> {{$value['term']['term_name']}} </td>
                <td align="center"> {{$value['available_at']}} </td>
                <td align="center"> {{$value['accepted_at']}} </td>
                <td align="center"> {{$value['accept']['label']}} </td>
            </tr>  
            @endforeach
        </tbody>
    </table>

</div> 
@endsection