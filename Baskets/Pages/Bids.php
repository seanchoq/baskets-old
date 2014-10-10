<?php
namespace Baskets\Pages;
class Bids
{

///////////////////////////////////////////////

//////////     Page Display Switch   //////////

///////////////////////////////////////////////

	public static function display()
	{
		switch (\Baskets\Tools\Tracker::$uri[2])
		{
			case 'list':
				self::lister();
				break;
			case 'add':
				self::add();
				break;
			case 'bid':
				self::bid();
				break;
			case 'old':
				self::old();
				break;
			default:
				Framework::$newurl = 'bids/list';
				self::lister();
				break;
		}
	}




///////////////////////////////////////

//////////     BIDS LIST    //////////

///////////////////////////////////////

	public static function lister()
	{
		Framework::page_header('Bids | Baskets');
	?>
		<div class='main-viewer' id='main-viewer'>
			<div class='dash-box'>
				<div class='dash-box-header'>
					<h1><i class="fa fa-leaf"></i> All Bids</h1>
					<a href='<?=MY_URL?>/bids/add' class='add-button'>Add Bid</a>
				</div>
				<p>
					<table class='table-one'>

		<?
			// Print column titles
			$cols = array('id','bid');
			echo '<tr>';
			foreach($cols as $col)
			{
				echo "<td>$col</td>";				
			}
			echo '</tr>';

			// Connect to DB and print bids
			$db = \Baskets\Tools\Database::getConnection();
			$stm = $db->prepare("SELECT * FROM bids");
			$stm->execute();
			while($bids = $stm->fetch())
			{
				echo "<tr class='list-item' onclick=\"document.location = '".MY_URL."/bids/bid/".$bids['id']."'\">";
				foreach($cols as $col)
				{
					echo '<td>' . $bids[$col] . '</td>';
				}
				echo '</tr>';
			}
		?>

					</table>
				</p>
			</div>
		</div>
<?php
		Framework::page_footer();
	}







///////////////////////////////////////

//////////     ADD Bid    ///////////

///////////////////////////////////////

	public static function add()
	{
		Framework::page_header('Add Bid | Baskets');
	?>
		<div class='main-viewer' id='main-viewer'>
			<div class='dash-box'>
				<div class='dash-box-header'>
					<h1><i class="fa fa-leaf"></i> Add Bid</h1>
					<a href='<?=MY_URL?>/bids/list' class='add-button'>List Bids</a>
				</div>
				<p>
					<form class='formula-one'>
						<div class='line'>
							<div class='group'>
								<label for='supplier'>Supplier</label>
								<input type='text' name='supplier' id='supplier'>
							</div>
						</div>	
						<div class='line'>
							<div class='group'>
								<label for='bid'>Bid</label>
								<span><input type='text' name='bid' id='bid'></span>
							</div>
						</div>
						<div class='bid-pp-cont' id='bid-pp-cont'>
							<div class='bid-pp-line'>
								<div class='bid-part'>Part ID</div>
								<div class='bid-name'>Name</div>
								<div class='bid-price'>Price</div>
							</div>
							<div class='bid-pp-line' id='pp0' style='display:none'>
								<div class='bid-part'><input type='text' name='part0' id='part0' data-pn='0' onfocus="checkpp(this)"></div>
								<div class='bid-name'>item name here</div>
								<div class='bid-price'><input type='number' step="0.01" name='price0' id='price0' data-pn='0' onfocus="checkpp(this)"></div>
							</div>
						</div>

						<div class='input-wrap'>
							<input type='hidden' name='job' value='add_bid'>
							<input type='hidden' name='pp' id='pp' value='1'>
							<input type='submit'>
						</div>
					</form>
					<script>
						$( 'form' ).submit( function( event ) {
							event.preventDefault();
							var formdata = JSON.stringify($( this ).serializeObject());
							sender('bids',formdata);
						});

var numparts = 0;

function checkpp(elem){
	var mynum = elem.getAttribute('data-pn');
	if (mynum == numparts) addpp();
}

function addpp(){
	var pp = document.getElementById('pp0').cloneNode(true);
	numparts++;
	pp.id = 'pp' + numparts;
	pp.style.display = 'block';
	pp.childNodes[1].firstChild.id = 'part' + numparts;
	pp.childNodes[1].firstChild.name = 'part' + numparts;
	pp.childNodes[1].firstChild.setAttribute('data-pn', numparts);
	console.log( pp.childNodes[1].firstChild);
	pp.childNodes[5].firstChild.id = 'price' + numparts;
	pp.childNodes[5].firstChild.name = 'price' + numparts;
	pp.childNodes[5].firstChild.setAttribute('data-pn', numparts);
	document.getElementById('bid-pp-cont').appendChild(pp);
	document.getElementById('pp').value = numparts;
	tahead('part'+numparts);
}

$(function(){
	addpp();
	console.log('oneo');
});







var substringMatcher = function(strs) {
  return function findMatches(q, cb) {
    var matches, substrRegex;
 
    // an array that will be populated with substring matches
    matches = [];
 
    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');
 
    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    $.each(strs, function(i, str) {
      if (substrRegex.test(str)) {
        // the typeahead jQuery plugin expects suggestions to a
        // JavaScript object, refer to typeahead docs for more info
        matches.push({ value: str });
      }
    });
 
    cb(matches);
  };
};





var suppliers = [<? self::print_suppliers() ?>];

$('#supplier').typeahead({
	hint: true,
	highlight: true,
	minLength:0
},
{
	name: 'suppliers',
	displayKey: 'value',
	source: substringMatcher(suppliers)
});


var parts = [<? self::print_parts() ?>];
function tahead(docid){
	$('#'+docid).typeahead({
		hint: true,
		highlight: true,
		minLength:1
	},
	{
		name: 'part',
		displayKey: 'value',
		source: substringMatcher(parts)
	});
}




/*
					// Autocomplete Supplier Name
						var acs = completely(document.getElementById('supplier'),{
								fontSize: '24px',
								fontFamily: 'Arial',
								color: '#000',
							});
						acs.options = [<? self::print_suppliers() ?>];
						acs.input.onfocus= function() { 
							acs.repaint();
						}

					// Autocomplete Part

						var acp = completely(document.getElementById('part'),{
								fontSize: '24px',
								fontFamily: 'Arial',
								color: '#000',
							});
						acp.options = [<? self::print_parts() ?>];
						acp.input.onfocus= function() { 
							acp.repaint();
						}

*/
					</script>
				</p>
			</div>
		</div>
	<?	Framework::page_footer();
	}



///////////////////////////////////////

//////////     Update Bid    ///////////

///////////////////////////////////////

	public static function bid()
	{
		$stm = \Baskets::$db->prepare("SELECT * FROM bids WHERE id=?");
		$stm->execute(array(\Baskets\Tools\Tracker::$uri[3]));
		$bid = $stm->fetch();

		$stm = \Baskets::$db->prepare("SELECT supplier FROM suppliers WHERE id=?");
		$stm->execute(array($bid['supplierid']));
		$res = $stm->fetch();
		$supplier = $res['supplier'];

		Framework::page_header('Update Bid ' . $bid['bid'] . ' | Baskets');
	?>
		<div class='main-viewer' id='main-viewer'>
			<div class='dash-box'>
				<div class='dash-box-header'>
					<h1><i class="fa fa-leaf"></i> Update Bid</h1>
					<a href='<?=MY_URL?>/bids/list' class='add-button'>List Bids</a>
				</div>
				<p>
					<form class='formula-one'>
						<div class='line'>
							<div class='group'>
								<label for='supplier'>Supplier</label>
								<input type='text' name='supplier' id='supplier' value='<?=$supplier?>'>
							</div>
						</div>	
						<div class='line'>
							<div class='group'>
								<label for='bid'>Bid</label>
								<span><input type='text' name='bid' id='bid' value='<?=$bid['bid']?>'></span>
							</div>
						</div>
						<div class='bid-pp-cont' id='bid-pp-cont'>
							<div class='bid-pp-line'>
								<div class='bid-part'>Part ID</div>
								<div class='bid-name'>Name</div>
								<div class='bid-price'>Price</div>
							</div>
							<div class='bid-pp-line' id='pp0' style='display:none'>
								<div class='bid-part'><input type='text' name='part0' id='part0' data-pn='0' onfocus="checkpp(this)"></div>
								<div class='bid-name'>item name here</div>
								<div class='bid-price'><input type='number' name='price0' id='price0' data-pn='0' onfocus="checkpp(this)"></div>
							</div>
						<?
							$astm = \Baskets::$db->prepare("SELECT * FROM parts WHERE id=?");
							$stm = \Baskets::$db->prepare("SELECT * FROM bidparts WHERE bidid=?");
							$stm->execute(array($bid['id']));
							$pp=0;
							while($bp = $stm->fetch()) {
								$pp++;
								$astm->execute(array($bp['partid']));
								$part = $astm->fetch();
								?>
							<div class='bid-pp-line' id='pp<?=$pp?>'>
								<div class='bid-part'><input value='<?=$part['partid']?>' type='text' name='part<?=$pp?>' id='part<?=$pp?>' data-pn='<?=$pp?>' onfocus="checkpp(this)"></div>
								<div class='bid-name'><?=$part['partname']?></div>
								<div class='bid-price'><input value='<?=$bp['price']?>' type='number' step="0.01" name='price<?=$pp?>' id='price<?=$pp?>' data-pn='<?=$pp?>' onfocus="checkpp(this)"></div>
							</div>
							<script>
								$(function(){ tahead('part<?=$pp?>'); });
							</script>
							<? } ?>
						</div>

						<div class='input-wrap'>
							<input type='hidden' name='job' value='update_bid'>
							<input type='hidden' name='bidid' value='<?=$bid['id']?>'>
							<input type='hidden' name='pp' id='pp' value='<?=$pp?>'>
							<input type='submit' value='Update'>
						</div>
					</form>
					<script>
						$( 'form' ).submit( function( event ) {
							event.preventDefault();
							var formdata = JSON.stringify($( this ).serializeObject());
							sender('bids',formdata);
						});

var numparts = <?=$pp?>;

function checkpp(elem){
	var mynum = elem.getAttribute('data-pn');
	if (mynum == numparts) addpp();
}

function addpp(){
	var pp = document.getElementById('pp0').cloneNode(true);
	numparts++;
	pp.id = 'pp' + numparts;
	pp.style.display = 'block';
	pp.childNodes[1].firstChild.id = 'part' + numparts;
	pp.childNodes[1].firstChild.name = 'part' + numparts;
	pp.childNodes[1].firstChild.setAttribute('data-pn', numparts);
	console.log( pp.childNodes[1].firstChild);
	pp.childNodes[5].firstChild.id = 'price' + numparts;
	pp.childNodes[5].firstChild.name = 'price' + numparts;
	pp.childNodes[5].firstChild.setAttribute('data-pn', numparts);
	document.getElementById('bid-pp-cont').appendChild(pp);
	document.getElementById('pp').value = numparts;
	tahead('part'+numparts);
}


var substringMatcher = function(strs) {
  return function findMatches(q, cb) {
    var matches, substrRegex;
 
    // an array that will be populated with substring matches
    matches = [];
 
    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');
 
    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    $.each(strs, function(i, str) {
      if (substrRegex.test(str)) {
        // the typeahead jQuery plugin expects suggestions to a
        // JavaScript object, refer to typeahead docs for more info
        matches.push({ value: str });
      }
    });
 
    cb(matches);
  };
};





var suppliers = [<? self::print_suppliers() ?>];

$('#supplier').typeahead({
	hint: true,
	highlight: true,
	minLength:0
},
{
	name: 'suppliers',
	displayKey: 'value',
	source: substringMatcher(suppliers)
});


var parts = [<? self::print_parts() ?>];
function tahead(docid){
	$('#'+docid).typeahead({
		hint: true,
		highlight: true,
		minLength:1
	},
	{
		name: 'part',
		displayKey: 'value',
		source: substringMatcher(parts)
	});
}


					</script>
				</p>
			</div>
		</div>
	<?	Framework::page_footer();
	}

	public static function print_suppliers(){
		$stm = \Baskets::$db->prepare("SELECT supplier FROM suppliers WHERE valid=true");
		$stm->execute();
		$first = true;
		while($res = $stm->fetch()){
			if(!$first) $comma = ',';
			else{
				$comma = '';
				$first = false;
			}
			echo "$comma\"".addslashes($res['supplier'])."\"";
		}
	}


	public static function print_parts(){
		$stm = \Baskets::$db->prepare("SELECT partid FROM parts");
		$stm->execute();
		$first = true;
		while($res = $stm->fetch()){
			if(!$first) $comma = ',';
			else{
				$comma = '';
				$first = false;
			}
			echo "$comma\"".addslashes($res['partid'])."\"";
		}
	}


}
