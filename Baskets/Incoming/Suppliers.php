<?
namespace Baskets\Incoming;
class Suppliers
{
	public static $info;

	public static function engine()
	{
		$rawinfo = isset($_POST['what']) ? $_POST['what'] : $_GET['what'];
		self::$info = json_decode($rawinfo,true);
		print_r(self::$info);
		switch(self::$info['job'])
		{
			case 'add_supplier':
				self::add_supplier();
				break;
			case 'update_supplier':
				self::update_supplier();
				break;
			default:
				echo 'you had NO job';
				break;
		}
	}


	public static function add_supplier()
	{
		$stm = \Baskets::$db->prepare("INSERT INTO suppliers(dt,dtu,valid,supplier,address,email,fax,phone) VALUES(NOW(),NOW(),true,?,?,?,?,?)");
		$ins = $stm->execute(array(	self::$info['supplier'],
												self::$info['address'],
												self::$info['email'],
												self::$info['fax'],
												self::$info['phone']));
		if($ins) echo 'supplier has been added';
		else echo 'could not add part :(';
	}


	public static function update_supplier()
	{
		$stm = \Baskets::$db->prepare("UPDATE suppliers SET dtu=NOW(),supplier=?,address=?,email=?,fax=?,phone=? WHERE id=?");
		$up = $stm->execute(array(self::$info['supplier'],
									self::$info['address'],
									self::$info['email'],
									self::$info['fax'],
									self::$info['phone'],
									self::$info['entry-id']));
		if($up) echo 'supplier has been updated';
		else echo 'there where an error..';
	}
}
