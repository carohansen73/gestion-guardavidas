@props(['url'])
<tr>
    <td class="header" align="center">

        <a href="{{ $url }}" style="display: inline-block; text-decoration: none;">

            <table cellpadding="0" cellspacing="0" role="presentation" align="center">
                <tr>
                    <td class="header-icono" align="center" valign="middle">
                        <img src="{{ asset('img/TSAS-logo.png') }}" alt="Logo" height="40" width="auto" style="display:block; margin:auto;">
                    </td>
                    <td class="header-texto" align="left" valign="middle">
                        <span class="header-titulo">{!! $slot !!}</span><br>
                        <span class="header-subtitulo">Municipio de Tres Arroyos</span>
                    </td>
                </tr>
            </table>

        </a>
    </td>
</tr>
