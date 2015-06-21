<?php
header('Content-Type: text/html; charset=utf-8');

$new = [[
'cells' => array(2,1,6,7)
, 'text' => 'Текст серого цвета'
, 'align' => 'center'
, 'valign' => 'center'
, 'color' => 'BEBEBE'
, 'bgcolor' => '000000'
],[
'cells' => array(15,10)
, 'text' => 'Текст фиолетового цвета'
, 'align' => 'center'
, 'valign' => 'center'
, 'color' => '7D26CD'
, 'bgcolor' => 'CFCFCF'
],[
'cells' => array(21,22,23,24,25)
, 'text' => 'Текст красного цвета'
, 'align' => 'left'
, 'valign' => 'center'
, 'color' => 'FF0000'
, 'bgcolor' => 'FFDEAD'
]
];

$table=htmlTable(5,5, $new);//первое количество рядов второе ячеек в ряду
echo $table;

function htmlTable($tr, $col, $arr1){
    //отсортировать по порядку объединения
    foreach($arr1 as $key=>$cells)
    {
       sort($arr1[$key]['cells']); 
    }
    //разбиваем на ряды таблицу
    $ids=1;
    $idArray=array();
    for($i=1;$i<=$tr;$i++)
    { 
        for($y=1;$y<=$col;$y++)
        {
           $row[$i][]=$ids;
           $idArray[]=$ids;
           $ids++; 
        }  
    } 
    // проверка на содержание одинаковых ячеек в разных объединениях
    if(count($arr1)>1)
    {
        for($i=0; $i<=count($arr1)-2; $i++)
        {
            $cheksame=array_intersect($arr1[$i]['cells'],$arr1[$i+1]['cells']);            
            if(!empty($cheksame)) 
            {
               return $html='Неправильное объединение ячеек. Одинаковые ячейки в разных объединениях'; 
            }
        }
    }
    //проверяем существует ли ячейка которую надо объединить
    for($i=0; $i<=count($arr1)-1; $i++)
    {
        $exist=array_diff($idArray,$arr1[$i]['cells']);

        if((count($exist))!=(count($idArray)-count($arr1[$i]['cells']))) 
        {
           return $html='Неправильное объединение ячеек. В объявленной таблице нет такой ячейки'; 
        }
    }  
   //выбераем объедененные ячейки чтоб потом их пропустить
    $tabEl=[];
    foreach($arr1 as $ke=>$arr)
    {
        foreach($arr['cells'] as $value)
        {
            foreach($row as $key=> $val)
            {
                foreach($val as $K=>$v)
                {
                    if($value==$v)
                    {
                        $tabEl[$ke][$key].=','.$v;
                    }
                }
            }
        }
    }
    $countRow=[];
    $checkcol=[];
    //проверка объединения
    for($y=0; $y<count($tabEl);$y++)
    {
        foreach($tabEl[$y] as $k=>$v)
        {  
            $v  =   substr($v, 1);  
            $v  =   explode(",", $v);
            $checkcol[$y][]=$v[0];
            for($i=0; $i<count($v)-1; $i++ )
            {
                if($v[$i]+1!=$v[$i+1])
                {
                    return $html='Объединение ячеек невозможно!';
                }
            }
            $countRow[$y][]= substr_count($tabEl[$y][$k], ',');//сколько обънединять по горизонтали
        }
    }
    //проверка на правильное объединение  по вертикали 
    //первое число рядом ниже меньше первого числа рядом выше на количество ячеек в ряду
    for($y=0; $y<count($checkcol);$y++)
    {
        for($i=0; $i<count($checkcol[$y])-1; $i++)
        {
            if($checkcol[$y][$i]+$col!=$checkcol[$y][$i+1])
            {
                return $html='Объединение ячеек невозможно!';
            }
        }
    }
    //количество объединяемых ячеек в каждой строке должно быть одинаковым
    for($i=0; $i<count($countRow); $i++)
    {
        $tabElUniq=array_unique($countRow[$i]);
        if(count($tabElUniq)!=1){
            return $html='Объединение ячеек невозможно!';
        }
        else
        {
            $countTd[]=$countRow[$i][0];// определили количество colspan
        }
    }

    foreach($tabEl as $value)
    {
    $countCol[]=count($value);// определили количество rowspan
    }
    $deliteTd=[];
    //задаем атрибуты таблицы
    foreach($arr1 as $properties)
    {
            $firstElement[]=$properties['cells'][0];//первый ячейка которая объединяется
            unset($properties['cells'][0]);
            $deliteTd=array_merge($deliteTd, $properties['cells']); //ячейки которые учавствуют в объединении кроме первой
            $align[]='align="'.$properties['align'].'"';
            $valign[]='valign="'.$properties['valign'].'"';
            $colortext[]='<font color="'.$properties['color'].'">'.$properties['text'].'</font>' ;
            $bgcolor[]='bgcolor="'.$properties['bgcolor'].'"';                
    }
    for($i=0; $i<count($arr1); $i++)
    {
    $rolspan[]='rowspan="'.$countCol[$i].'"';
    $colspan[]='colspan="'.$countTd[$i].'"';
    }
    $id=1;
    //формирование html таблицы
    $html=  '<!doctype html>
            <html>
                <head>
                <meta charset="UTF-8">
                <title>Генератор HTML таблицы</title>
                <link href="style.css" rel="stylesheet">
                </head>
                <body>
                    <div class="wrapper">
                        <p>Результат</p>
                        <table border="1">';  
                            for($i=0;$i<$tr;$i++)
                            {
                                $html.='<tr>';
                                for($y=1;$y<=$col;$y++)
                                {

                                    if(!in_array($id, $deliteTd) && !in_array($id, $firstElement))
                                    {    
                                        $html.='<td  class="tableTd">'.$id.'</td>';
                                        $id++;
                                    }
                                    elseif(in_array($id, $firstElement))
                                    {
                                        for($z=0; $z<count($firstElement); $z++)
                                        {
                                            if($id==$firstElement[$z])
                                            {

                                            $html.='<td class="tableTdUnion" '.$colspan[$z].' '.$rolspan[$z].' '.$align[$z].' '.$valign[$z].' '.$bgcolor[$z].'>'.$colortext[$z].'</td>';
                                            $id++;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $id++;
                                    }
                                }  
                                $html.='</tr>'; 
                            }
                        $html.='</table>
                    </div>
                </body>
            </html>';
 
    return $html;
   
}








