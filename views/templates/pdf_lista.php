<page backtop="30mm" backbottom="10mm" backleft="10mm" backright="10mm">
	<page_header>
        <table style="width: 100%;">
            <tr>
                <td style="width: 100%;text-align:left;">
                    {{ asset:image file="pdf/cintillo_header.png" style="width:100%;" }}
                    
                </td>
                
                
            </tr>
            
        </table>
    </page_header>
    
    <!--h4 style="text-align: center;">{{centro}}</h4-->
    <h2 style="text-align: center;">{{disciplina.nombre}}</h2>
    <p>
        PLANTEL: {{centro}}
       
    </p>
    <p> ASESOR: {{disciplina.asesor}}</p>
     <br />
    <table width="100%" >
        <thead>
            <tr>
                <th width="20" style="border-bottom: #a6ce39 2px solid;padding: 3px;">NO.</th>
                
                <th width="300" style="border-bottom: #a6ce39 2px solid;padding: 3px;">NOMBRE DEL PARTICIPANTE</th>
                <th width="100" style="border-bottom: #a6ce39 2px solid;padding: 3px;text-align: center;">SEMESTRE</th>
                <th width="100" style="border-bottom: #a6ce39 2px solid;padding: 3px;">MATRICULA</th>
                <th width="100" align="center" style="border-bottom: #a6ce39 2px solid;padding: 3px;">SEXO</th>
            </tr>
        </thead>
        <tbody>
        {{registros}}
            <tr>
                <td>{{ helper:count }}</td>
                
                <td style="padding: 3px;vertical-align: middle;"> {{participante}}</td>
                <td style="padding: 3px;text-align: center;">{{grado}}</td>
                <td style="padding: 3px;">{{extra.matricula}}</td>
                <td style="padding: 3px;" align="center">
                {{ if sexo == 1}}
                HOMBRE
                {{else}}
                    MUJER
                {{endif}}
                </td>
               
            </tr>
        {{/registros}}
        </tbody>
    </table>
    <br />
     
    
 </page>