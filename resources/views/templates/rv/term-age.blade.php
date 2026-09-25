@extends('templates.rv.model')
@section('body')
<div class="container-fluid">

    <!-- {{-- Headeer --}} -->
    <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(public_path('assets/img/logo-placeholder.svg'))) }}"
    style="position: absolute; top:-40px; left:-5px; height: 50px; margin: 10px;">

    <div style="text-align: center; width: 100%;">
        <h2 style="display: inline-block; margin-top: 0; margin-bottom: 0; width: 100%;">TERMO DE PACTUAÇÃO DE METAS
        </h2>
    </div>

    <hr />

    <table border="0">
        <tr>
            <td width="70px">Termo:</td>
            <td colspan="2" style="font-weight: bold">
                {{$term->term_name}}
            </td>
        </tr>
        <tr>
            <td width="70px">Setor:</td>
            <td colspan="2" style="font-weight: bold">{{$term->sectorN1->id}} - {{$term->sectorN1->name}}</td>
        </tr>

        @if($term->sectorN2?->id <> 0)
        <tr>
            <td>Sub-setor:</td>
            <td colspan="2" style="font-weight: bold">{{$term->sectorN2->id}} - {{$term->sectorN2->name}}</td>
        </tr>
         @endif
       
       
        <tr>
            <td colspan="3">Periodo de apuração: <b>{{$term->evaluation_interval}}</b></td>
            
        </tr>
    </table>



    
    @if($term->signed)

    <table border="0"  cellspacing='0' style="margin-top: 5px; width: 100%" >
        <tr class="table_header" style=" background-color: rgba('0,0,0,0.1' }}, 0.5); color: rgba({{$term->signed['accept']['rgb'] ?? '0,0,0' }}, 1);" align="left">
            <td style="padding: 3px 7px;" > Termo <b>{{$term->signed['accept']['label']}}</b> em  <b>{{$term->signed['created_at'] ?? ''}}</b> por <b>{{$term->signed['username'] ?? ''}} - {{$term->signed['user']['name'] ?? ''}} </b>
                <br>Protocolo:<b> {{$term->signed['uuid'] ?? ''}} </b>
            </td>
        </tr>
    </table> 
    @endif

    <p>
        Caro colaborador, segue meta individual e indicadores que serão considerados para pagamento de sua campanha de
        premiação
    </p>

    <table border="1" cellspacing='0' style="margin-left: auto;    margin-right: auto;   width: 100%;">
        <thead>
            <tr class="table_header basket">
                @if($term->max_accelerated > 0)
                <td colspan="5" align="center" class="uppercase">Cesta de indicadores para premiação</td>
                @else 
                <td colspan="4" align="center" class="uppercase">Cesta de indicadores para premiação</td>
                @endif
               
            </tr>
            <tr class="table_header basket">
                <td align="center" width="50%" >Indicador</td>
                <td align="center">Faixa</td>
                <td align="center">Critério</td>
                <td align="center">Valor</td>
                @if($term->max_accelerated > 0)
                <td align="center">Val. Acelerado</td>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ( $term->basket as $key => $value )
            <tr>

                <td style="padding-left: 5px"> {{$value['indicator_id']}} - {{$value['indicator_name']}} </td>
                <td align="center"> {{$value['range']}} </td>
                <td align="center"> {{$value['operation']}} {{$value['target_label']}} ({{$value['calc']['label']}}) </td>
                <td align="center"> R$ {{ number_format($value['value'], 2, ',', '.') }} </td>
                @if($term->max_accelerated > 0)
                <td align="center"> R$ {{ number_format($value['value_accelerated'], 2, ',', '.') }} </td>
                @endif
                @endforeach
        </tbody>
    </table>

    <p class="explan">
        O colaborador receberá a produtividade multiplicada pelo valor da Faixa atingida.
        <br>Premiação = Produtividade Final * Valor da Faixa
    </p>

    @if (count($term->accelerator) >0)
    <table border="1" cellspacing='0' style="margin-left: auto;    margin-right: auto;   width: 100%;">
        <thead>
            <tr class="table_header accelerator">
                <td colspan="4" align="center" class="uppercase">Aceleradores para premiação</td>
            </tr>
            <tr class="table_header accelerator">
                <td align="center" width="60%">Indicador</td>
                <td align="center">Faixa</td>
                <td align="center">Meta</td>
                <td align="center">% Acelerado</td>
            </tr>
        </thead>
        <tbody>
            @foreach ( $term->accelerator as $key => $value )
            <tr>
                <td style="padding-left: 5px"> {{$value['indicator_id']}} - {{$value['indicator_name']}} </td>
                <td align="center"> {{$value['range']}} </td>
                <td align="center"> {{$value['operation']}} {{$value['target_label']}}  ({{$value['calc']['label']}}) </td>
                <td align="center"> {{ $value['value'] }}% </td>    
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <p class="explan">
        O valor apurado na <b>Cesta</b> é <b>incrementado</b> progressivamente através do <b>Acelerador</b>. Cada meta
        alcançada adiciona um percentual não à sua remuneração.<br>
        Exemplo: Se você atingir duas metas, sendo a primeira com um acréscimo de <b>2%</b> e a segunda com <b>3%</b>, o
        aumento total na sua remuneração será de <b>5%</b>
    </p> --}}

    <p class="explan">
        O valor apurado na <b>Cesta</b> é <b>incrementado</b> progressivamente através do <b>Acelerador</b>. Conforme o atingimento da meta
        adiciona um percentual não cumulativo para o mesmo indicador de sua premiação.<br>
    </p>

    @endif

 
    @if (count($term->deflator) >0)
   
    <table border="1" cellspacing='0' style="margin-left: auto;    margin-right: auto;  width: 100%;">
        <thead>
            <tr class="table_header deflator">
                <td colspan="4" align="center" class="uppercase">Deflatores para premiação</td>
            </tr>
            <tr class="table_header deflator">
                <td align="center" width="60%">Indicador</td>
                <td align="center">Faixa</td>
                <td align="center">Limit</td>
                <td align="center">% Deflacionado</td>
            </tr>
        </thead>
        <tbody>
            @foreach ( $term->deflator as $key => $value )
             <tr>
                <td style="padding-left: 5px"> {{$value['indicator_id']}} - {{$value['indicator_name']}} </td>
                <td align="center"> {{$value['range']}} </td>
                <td align="center"> {{$value['operation']}} {{$value['target_label']}}  ({{$value['calc']['label']}})</td>
                <td align="center">  {{ $value['value_label']}}% </td>    
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="explan">
        O valor apurado na <b>Cesta</b> é <b>ajustado para baixo</b> progressivamente através do <b>Deflator</b>. Cada
        condição atendida resulta em uma redução <b>cumulativa</b> na sua remuneração.<br>
        Exemplo: Se duas condições específicas forem atendidas, sendo a primeira com uma redução de <b>1%</b> e a
        segunda com <b>2%</b>, a diminuição total na sua remuneração será de <b>5%</b>.
    </p>
    @endif
  

    <table border="1" cellspacing='0' style="margin-left: auto;    margin-right: auto;  width: 100%;">
        <thead>
            <tr class="table_header knockout">
                <td colspan="3" align="center" class="uppercase">Critérios eliminatórios</td>
            </tr>
            <tr class="table_header knockout">
                <td align="center">Indicador</td>
                <td align="center">Critério</td>
                <td align="center">% perda de premiação</td>
            </tr>
        </thead>
        <tbody>
            
            <tr>
                <td style="padding-left: 5px"> Não aceite deste termo de pactuação</td>
                <td align="center"> - </td>
                <td align="center">100%</td>
            </tr>
            @foreach ( $term->elimination as $key => $value )
           
            <tr>
                <td style="padding-left: 5px"> {{$value['indicator_id']}} - {{$value['indicator_name']}} </td>
                <td align="center"> {{$value['operation']}} {{$value['target_label']}}   ({{$value['calc']['label']}})</td>
                <td align="center"> 100% </td>    
             
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="explan">
        Caso ocorra qualquer critério eliminatório, o colaborador será <b>eliminado da premiação</b>.
    </p>

 
    <h5 style="margin: 4px 0;">REGRAS DE PAGAMENTO</h5>
    <ol class="payment-rules">
        
        
        @if (count($term->accelerator) > 0)
        <li>
           
            O colaborador só terá direito ao Acelerador se atingir a meta estabelecida e estiver elegível para
            receber premiação.

        </li>
        @endif
        
        @if (count($term->deflator) > 0)
        <li>
          
            Caso o colaborador atenda aos critérios estabelecidos pelos deflatores, ocorrerá uma redução correspondente no valor de sua premiação.

        </li>
        @endif
        <li>
            <!-- <span class="title">Cálculo do Pagamento:</span> -->
            O valor a ser recebido será calculado somando a <b>Cesta</b> com os ajustes do <b>Acelerador</b> e
            do <b>Deflator</b> aplicados à <b>Cesta</b>.

            {{-- <br><b>Fórmula:</b> Cesta + (Acelerador × Cesta) - (Deflator × Cesta). --}}
        </li>
        
        <li>
            Os valores da PREMIAÇÃO serão submetidos as regras do teto vigentes para o setores e cargos.
        </li>
        
        @isset($term->roof)
        <li>
            <!-- <span class="title">Limite Máximo de Pagamento:</span> -->

            O valor total a ser recebido pelo colaborador será limitado a um teto de <b>R$ {{number_format($term->roof, 2, ',', '.')}}</b>.

        </li>
        @endisset
        @isset($term->notes)
        <li>
            <pre class="pre">{{$term->notes}}</pre>

        </li>
        @endisset
    </ol>





    <h5 style="margin: 4px 0;">REGRAS DA PACTUAÇÃO</h5>
    <ol class="payment-rules">
     
       
        <li>
            A hierarquia e a célula consideradas para a apuração serão as do GIP OFICIAL diário da EMPRESA;
        </li>
        <li>
            DESLIGADOS: Em caso de desligamento ou pedido de demissão, a premiação será paga conforme a média de 3 meses do
            colaborador desconsiderando o mês anterior ao desligamento. Exemplo: colaborador desligado no mês 05,
            desconsidera o mês 04 e faz a média dos meses 1, 2 e 3 proporcional aos dias trabalhados. Somente estarão
            elegíveis a premiação, os colaboradores desligados com % Logado maior ou igual a 80%;
        </li>
        <li>
            Após conclusão da apuração deste termo e fechamento da folha de pagamento, havendo contestação, será
            avaliada e se considerada procedente, o valor reclamado será pago como premiação de contestação no fechamento da
            próxima folha;
        </li>
        <li>
            Em caso de identificação de falha crítica e/ou processos indevidos que tenham ocasionado ganho de incentivo,
            estes valores serão estornados seguindo regras de estorno da EMPRESA na primeira folha de pagamento após
            confirmação da venda ou processo;
        </li>
        <li>
            Resultados deverão ser considerados com duas casa decimais;
        </li>
        <li>
            O colaborador receberá o Acelerador conforme atingimento da meta do acelerador e só será elegível ao
            acelerador o colaborador que ganhar premiação;
        </li>
        <li>
            Se o colaborador estiver em treinamento/atividades do RH a jornada de trabalho a ser considerada será a
            original do piso.
        </li>
        @if ($term->apprentice == 0)
        <li>
            Aprendiz (menor ou maior de 18 anos) não participa da premiação.
        </li>
        @endif
    </ol>

</div> 
@endsection