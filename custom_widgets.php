<?php
$custom_dashboard_widgets = array(
    'my-dashboard-widget' => array(
        'title' => 'Monthly Stats',
        'callback' => 'dashboardWidgetContent'
    )
);
function dashboardWidgetContent() {

    $mfortuneDomain = esc_attr( get_option('mfortune_domain') );
    $casumoDomain = esc_attr( get_option('casumo_domain') );
    $skybetDomain = esc_attr( get_option('skybet_domain'));

    if($casumoDomain!=''){
        require('simple_html_dom.php');

        $casumo_views = esc_attr( get_option('casumo_views') );
        $casumo_clicks = esc_attr( get_option('casumo_clicks') );
        $casumo_revenue = esc_attr( get_option('casumo_revenue') );
        $casumo_ctr = esc_attr( get_option('casumo_ctr') );
        $casumo_signups = esc_attr( get_option('casumo_signups') );

        $casumo_date_from = esc_attr( get_option('casumo_date_from') );
        $casumo_date_to = esc_attr( get_option('casumo_date_to') );
        $to = '30-01-2018'; // last day of last month
        $from = '01-01-2018'; // first day of last month
        $from_date = date('Y-m-d', strtotime($casumo_date_from));
  	    $to_date = date('Y-m-d', strtotime($casumo_date_to));  // today
        $from_time = date('Ymd', strtotime($casumo_date_from));
  	    $to_time = date('Ymd', strtotime($casumo_date_to));  // today
        $casumo_key = esc_attr( get_option('casumo_key') );
        if(!$casumo_key || !$casumo_date_from || !$casumo_date_to) return false;
        $json = file_get_html('https://casumo.dataexport.netrefer.com/v2/export/reports/affiliate/MarketingSourceDailyFiguresForXML?yearmonthdayfrom='.$from_time.'&yearmonthdayto='.$to_time.'&productID=all&advertiserID=1&PublishPointID=all&authorization='.$casumo_key)->plaintext;
        $obj = json_decode($json);


        $cpaTotal = 0;
        $views = 0;
        $clicks = 0;
        $ctr = 0;
        $signups = 0;

 	    $commissions = array();
        $viewsarr = array();
        $clicksarr = array();
 	    $date = array();

        foreach($obj as $o){

            $commissions[] = $o->{'Net Revenue'};
            $viewsarr[] = $o->{'Views'};
            $clicksarr[] = $o->{'Clicks'};
            $date[] = $o->{'Date'};

            $cpaTotal += $o->{'Net Revenue'};
            $views += $o->{'Views'};
            $clicks += $o->{'Clicks'};
            $ctr += $o->{'CTR'};
            $signups += $o->{'Signups'};
        }

        $datastrrevenue =  array();
        $datastrviews = array();
        $datastrclicks = array();
        $datastrctr = array();
        $datastrsignups = array();
    	$c=0;
    	foreach ($date as $value) {
    		$datastrrevenue[$value] = $datastrrevenue[$value] + $commissions[$c];
            $datastrviews[$value] = $datastrviews[$value] + $viewsarr[$c];
            $datastrclicks[$value] = $datastrclicks[$value] + $clicksarr[$c];
            $datastrctr[$value] = $datastrctr[$value] + $ctrarr[$c];
            $datastrsignups[$value] = $datastrsignups[$value] + $signupsarr[$c];
    		$c++;
    	}
    	//ksort($datastr);
        /*echo '<pre>';
        print_R($date);
        echo '</pre>';
        echo '<pre>';
        print_R($datastrrevenue);
        echo '</pre>';
        exit;*/

        ?>

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
           <div id="chart_div11"></div>
           <div>
           	<?php if($casumo_revenue){ echo 'Net Revenue:'.$cpaTotal; } ?>
            <?php if($casumo_clicks){ echo 'Views:'.$views; } ?>
            <?php if($casumo_views){ echo 'Clicks:'.$clicks; } ?>
            <?php if($casumo_ctr){ echo 'CTR:'.$ctr; } ?>
            <?php if($casumo_signups){ echo 'Signups:'.$signups; } ?>
            <br/>
           </div>
        <script type="text/javascript">
          google.charts.load('current', {'packages':['bar']});
          google.charts.setOnLoadCallback(drawChart);
          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Date'<?php if($casumo_revenue){ echo ",'Net Revenue'"; } ?><?php if($casumo_views){ echo ",'Views'"; } ?><?php if($casumo_clicks){ echo ",'Clicks'"; } ?><?php if($casumo_ctr){ echo ",'CTR'"; } ?><?php if($casumo_signups){ echo ",'Signups'"; } ?>],
              <?php $tmpc = 0; ?>
              <?php foreach($date as $k => $v): ?>

              <?php if($casumo_revenue){ $v_revenue = $datastrrevenue[$v];
                      if(trim($v_revenue)=='')
                          $v_revenue = 0; } ?>
              <?php $v_views = $datastrviews[$v];
                      if(trim($v_views)=='')
                          $v_views = 0; ?>
              <?php $v_clicks = $datastrclicks[$v];
                      if(trim($v_clicks)=='')
                          $v_clicks = 0; ?>
              <?php $v_ctr = $datastrctr[$v];
                      if(trim($v_ctr)=='')
                          $v_ctr = 0; ?>
              <?php $v_signups = $datastrsignups[$v];
                      if(trim($v_signups)=='')
                          $v_signups = 0; ?>
              [<?php echo "'".$v."'".($casumo_revenue?','.$v_revenue:'').($casumo_views?','.$v_views:'').($casumo_clicks?','.$v_clicks:'').($casumo_ctr?','.number_format($v_ctr / $tmpc):'').($casumo_signups?','.$v_signups:'')?>],

              <?php $tmpc++; ?>
              <?php endforeach;?>
            ]);

            var options = {
              chart: {
                title: 'Montly Stats',
                subtitle: 'Dates: <?php echo $from_date; ?> - <?php echo $to_date; ?>',
              },
              bars: 'horizontal', // Required for Material Bar Charts.
              hAxis: {format: 'decimal'},
              height: 400,
              colors: ['red'],
               isStacked: true
            };

            var chart = new google.charts.Bar(document.getElementById('chart_div11'));

            chart.draw(data, google.charts.Bar.convertOptions(options));

          }
        </script>
        <?php
    }
    /*
	//https://affiliatehub.skybet.com/api/affreporting.asp?key=4970467ec1322fe3ee154fc8fee2a061&reportname=AccountReport&reportformat=xml&reportmerchantid=0&reportstartdate=2016/9/1&reportenddate=2016/9/28
    if($mfortuneDomain!=''):?>
    <?php
	  //$key = '84abf2fc10ee1cadfb7914a0419c47d6';
	  $key = esc_attr( get_option('key1') );
	  $site = 'http://affiliates.mfortunepartners.com';
	  $from = date('Y/m/d', strtotime("first day of this month"));
	  $to = date('Y/m/d', strtotime("last day of this month"));  // today
	  $report='ACIDReport'; //report name

	  ////////////////
	  //merchant ids//
	  //mFortune = 2
	  //PocketWin = 3
	  //Mr Spin = 4
	  //all = 0

	 $chartdatavalue =  esc_attr( get_option('chart_data') );

	  $ready = 1;

	  $merchant_id = 2;

	  $url = $site.'/api/affreporting.asp?key='.$key.'&reportname='.$report.'&reportformat=xml&reportmerchantid='.$merchant_id.'&reportdisplayby=Date'.'&reportstartdate='.$from.'&reportenddate='.$to;
	  $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', file_get_contents($url));



	  $xml = simplexml_load_string($clean_xml);
	  $obj = json_decode(json_encode($xml));

	  //echo $url;

	   $cpaTotal = 0;
	   $merchants = array();
	   $commissions = array();
	   $date = array();
	try{
	    foreach($obj->Body->reportresponse->row as $row)
	    {

	        $CPACommission = (int)$row->CPACommission;
	        //if($CPACommission > 0)
	        //{

	        $merchant = $row->merchantname;
	        $Product1NetRevenue = $row->Product1NetRevenue;

	       $merchants[] = $merchant;
	       $commissions[] = $CPACommission;
	       $period = $row->period;

	       $date[] = date("Y-m-d", strtotime($period));

	          //echo "<hr>";
	          $cpaTotal += $CPACommission;
			//}

	    }
	}
	catch(Exception $ex)
	{
		echo $ex->getMessage();
	}
	$datastr =  array();
	$c=0;
	foreach ($date as $value) {
		$datastr[$value] = $datastr[$value] + $commissions[$c];
		$c++;
	}
	ksort($datastr);


     $merchant_id = 3;

	  $url = $site.'/api/affreporting.asp?key='.$key.'&reportname='.$report.'&reportformat=xml&reportmerchantid='.$merchant_id.'&reportdisplayby=Date'.'&reportstartdate='.$from.'&reportenddate='.$to;
	  $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', file_get_contents($url));



	  $xml = simplexml_load_string($clean_xml);
	  $obj = json_decode(json_encode($xml));

	  //echo $url;

	   $cpaTotal2 = 0;
	   $merchants = array();
	   $commissions = array();
	   $date = array();
	try{
	    foreach($obj->Body->reportresponse->row as $row)
	    {

	        $CPACommission = (int)$row->CPACommission;
	        //if($CPACommission > 0)
	        //{

	        $merchant = $row->merchantname;
	        $Product1NetRevenue = $row->Product1NetRevenue;

	       $merchants[] = $merchant;
	       $commissions[] = $CPACommission;
	       $period = $row->period;
	       $date[] = date("Y-m-d", strtotime($period));

	          //echo "<hr>";
	          $cpaTotal2 += $CPACommission;
			//}

	    }
	}
	catch(Exception $ex)
	{
		echo $ex->getMessage();
	}
	$datastr1 =  array();
	$c=0;
	foreach ($date as $value) {
		$datastr1[$value] = $datastr1[$value] + $commissions[$c];
		$c++;
	}
	ksort($datastr1);


	 $merchant_id = 4;

	  $url = $site.'/api/affreporting.asp?key='.$key.'&reportname='.$report.'&reportformat=xml&reportmerchantid='.$merchant_id.'&reportdisplayby=Date'.'&reportstartdate='.$from.'&reportenddate='.$to;
	  $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', file_get_contents($url));



	  $xml = simplexml_load_string($clean_xml);
	  $obj = json_decode(json_encode($xml));

	  //echo $url;

	   $cpaTotal3 = 0;
	   $merchants = array();
	   $commissions = array();
	   $date = array();
	try{
	    foreach($obj->Body->reportresponse->row as $row)
	    {

	        $CPACommission = (int)$row->CPACommission;
	        //if($CPACommission > 0)
	        //{

	        $merchant = $row->merchantname;
	        $Product1NetRevenue = $row->Product1NetRevenue;

	       $merchants[] = $merchant;
	       $commissions[] = $CPACommission;
	       $period = $row->period;
	       $date[] = date("Y-m-d", strtotime($period));

	          //echo "<hr>";
	          $cpaTotal3 += $CPACommission;
			//}

	    }
	}
	catch(Exception $ex)
	{
		echo $ex->getMessage();
	}
	$datastr2 =  array();
	$c=0;
	foreach ($date as $value) {
		$datastr2[$value] = $datastr2[$value] + $commissions[$c];
		$c++;
	}
	ksort($datastr2);
    ?>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
       <div id="chart_div"></div>
       <div>
       	mFortune: <?php echo $cpaTotal; ?> | PocketWin: <?php echo $cpaTotal2; ?> | Mr Spin: <?php echo $cpaTotal3; ?><br/>
       </div>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'mFortune', 'PocketWin', 'Mr Spin'],
          <?php $tmpc = 0; ?>
          <?php foreach($datastr as $k => $v): ?>
          <?php $val = $datastr1[$k];
          		if(trim($val)=='')
          			$val = 0; ?>
          <?php $val2 = $datastr2[$k];
          		if(trim($val2)=='')
          			$val2 = 0; ?>
          [<?php echo "'".$k."',".$v.",".$val.",".$val2;?>],
          <?php $tmpc++; ?>
          <?php endforeach;?>
        ]);

        var options = {
          chart: {
            title: 'Montly CPACommission',
            subtitle: 'mFortune, PocketWin, and Mr Spin: <?php echo $from; ?> - <?php echo $to; ?>',
          },
          bars: 'horizontal', // Required for Material Bar Charts.
          hAxis: {format: 'decimal'},
          height: 400,
          colors: ['#1b9e77', '#d95f02', '#7570b3'],
           isStacked: true
        };

        var chart = new google.charts.Bar(document.getElementById('chart_div'));

        chart.draw(data, google.charts.Bar.convertOptions(options));

      }
    </script>
<?php endif; ?>
<?php
*/
/*
if($skybetDomain!=''):?>
    <?php
	  $key = '8e85b84452eb29ff1d51dc898ead1d4d';
	  //$key = esc_attr( get_option('key2') );
	  //skybet key: 4970467ec1322fe3ee154fc8fee2a061
	  $site = 'http://partners.nektanaffiliates.com/';
	  $from = date('Y/m/d', strtotime("first day of this month"));
	  $to = date('Y/m/d', strtotime("last day of this month"));  // today
	  $report='ACIDReport'; //report name

	  //$from = '2016/5/31';
	  //$to ='2016/9/1';

	  ////////////////
	  //merchant ids//
	  //mFortune = 2
	  //PocketWin = 3
	  //Mr Spin = 4
	  //all = 0

	 $chartdatavalue =  esc_attr( get_option('chart_data') );

	  $ready = 1;

	  $merchant_id = 3;

	  $url = $site.'/api/affreporting.asp?key='.$key.'&reportname='.$report.'&reportformat=xml&reportmerchantid='.$merchant_id.'&reportdisplayby=Date'.'&reportstartdate='.$from.'&reportenddate='.$to;
	  $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', file_get_contents($url));

	  //echo $url;


	  $xml = simplexml_load_string($clean_xml);
	  $obj = json_decode(json_encode($xml));

	  //echo $url;

	   $cpaTotal = 0;
	   $merchants = array();
	   $commissions = array();
	   $date = array();
	try{
	    foreach($obj->Body->reportresponse->row as $row)
	    {

	        $CPACommission = (int)$row->CPACommission;
	        //if($CPACommission > 0)
	        //{

	        $merchant = $row->merchantname;
	       // $Product1NetRevenue = $row->Product1NetRevenue;

	       $merchants[] = $merchant;
	       $commissions[] = $CPACommission;
	       $period = $row->period;

	       $date[] = date("Y-m-d", strtotime($period));

	          //echo "<hr>";
	          $cpaTotal += $CPACommission;
			//}

	    }
	}
	catch(Exception $ex)
	{
		echo $ex->getMessage();
	}
	$datastr =  array();
	$c=0;
	foreach ($date as $value) {
		$datastr[$value] = $datastr[$value] + $commissions[$c];
		$c++;
	}
	ksort($datastr);
	//print_r($datastr);
    ?>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <br/><br/>
       <div id="chart_div2"></div>
       <div>
       Sapphire Rooms: <?php echo $cpaTotal; ?><br/>
       </div>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Date', 'Sapphire Rooms'],
          <?php $tmpc = 0; ?>
          <?php foreach($datastr as $k => $v): ?>
          [<?php echo "'".$k."',".$v;?>],
          <?php $tmpc++; ?>
          <?php endforeach;?>
        ]);

        var options = {
          chart: {
            title: 'Montly CPACommission',
            subtitle: 'Sapphire Rooms: <?php echo $from; ?> - <?php echo $to; ?>',
          },
          bars: 'horizontal', // Required for Material Bar Charts.
          hAxis: {format: 'decimal'},
          //height: <?php echo intval(count($datastr))*15?>,
          height: 400,
          colors: ['#1b9e77'],
           isStacked: true
        };

        var chart = new google.charts.Bar(document.getElementById('chart_div2'));

        chart.draw(data, google.charts.Bar.convertOptions(options));

      }
    </script>
<?php endif; ?>

    <?php
    */
}
