<?php
class WoocreditTranslate{

	private $coding_key = "hardtoget"; // Key for coding data
	private $download_timeout = 172800; // 48 Hours

	//translation in english
	private $translations =  array(
		'yes' => array(
				'en-US' => 'Yes',
				'de-DE' => 'Ja'
		),
		'no' => array(
				'en-US' => 'No',
				'de-DE' => 'Nein'
		),
			'Credits' => array(
					'en-US' => 'Credits',
					'de-DE' => 'Credits'
			),
			'Credits amount' => array(
					'en-US' => 'Used credits amount',
					'de-DE' => 'Anzahl benutzte Credits'
			),
			'Quantity of products sold' => array(
					'en-US' => 'Quantity of products sold',
					'de-DE' => 'Anzahl verkaufte Produkte'
			),
			'Added credits' => array(
					'en-US' => 'Added credits',
					'de-DE' => 'Hinzugefügte Credits'
			),
			'Amount' => array(
					'en-US' => 'Amount',
					'de-DE' => 'Betrag'
			),
			'Price' => array(
					'en-US' => 'Price',
					'de-DE' => 'Preis'
			),
			'Buy Credits' => array(
					'en-US' => 'Buy Credits',
					'de-DE' => 'Credits kaufen'
			),
			'withoutloginMessage' => array(
					'en-US' => 'Please login to buy credit',
					'de-DE' => 'Bitte loggen Sie sich ein, um Credits zu kaufen'
			),
			'areYouSure' => array(
				'en-US' => 'Are you sure you want to buy this product?',
				'de-DE' => 'Sind Sie sicher, dass Sie dieses Produkt kaufen wollen?'
			),
			'Are you sure you want to delete credits?' => array(
					'en-US' => 'Are you sure you want to delete credits?',
					'de-DE' => 'Sind Sie sicher, dass Sie die Credits löschen wollen?'
			),
			'Products statistics' => array(
					'en-US' => 'Products statistics',
					'de-DE' => 'Produkten Statistik'
			),
			'Please select the user:' => array(
					'en-US' => 'Please select the user:',
					'de-DE' => 'Benutzer auswählen:'
			),
			'Please select the date:' => array(
					'en-US' => 'Please select the date:',
					'de-DE' => 'Datum auswählen:'
			),
			'Search' => array(
					'en-US' => 'Search',
					'de-DE' => 'Suchen'
			),
			'loginPlease' => array(
					'en-US' => 'You are not logged in',
					'de-DE' => 'Sie sind nicht angemeldet'
			),
			'User credits' => array(
					'en-US' => 'User Credits',
					'de-DE' => 'Benutzer Credits'
			),
			'User' => array(
					'en-US' => 'User',
					'de-DE' => 'Benutzer'
			),
			'Title' => array(
					'en-US' => 'Title',
					'de-DE' => 'Titel'
			),
			'Date' => array(
					'en-US' => 'Date',
					'de-DE' => 'Datum'
			),
			'User statistics' => array(
					'en-US' => 'User statistics',
					'de-DE' => 'Benutzer Statistik'
			),
			'Add credits' => array(
					'en-US' => 'Add credits',
					'de-DE' => 'Credits hinzufügen'
			),
			'Name' => array(
					'en-US' => 'User Name',
					'de-DE' => 'Benutzername'
			),
			'No Result' => array(
					'en-US' => 'No Result Found',
					'de-DE' => 'Kein Ergebnis gefunden'
			),
			'insufficient' => array(
					'en-US' => 'There are not enough credits',
					'de-DE' => 'Sie haben nicht genug Credits'
			),
			'user credit' => array(
					'en-US' => 'User Credit',
					'de-DE' => 'Benutzer Kreditkarten'
			),
			'info_download' => array(
					'en-US' => 'Product download link',
					'de-DE' => 'Link für Ihr Produkt'
			),
			'Last 30 days' => array(
					'en-US' => 'Last 30 days',
					'de-DE' => 'Letzte 30 Tage'
			),
			'Yesterday sold products' => array(
					'en-US' => 'Yesterday sold products',
					'de-DE' => 'Gestern verkaufte Produkte'
			),
			'Recent Product Purchases' => array(
					'en-US' => 'Recent Product Purchases',
					'de-DE' => 'Ihre gekaufte Produkte'
			),
			'Search the product' => array(
					'en-US' => 'Search the product',
					'de-DE' => 'Produkt suchen'
			),
			'email' => array(
				'en-US' => '<p>You can download the files during next 48 hours</p><p>[filedownload]</p>',
				'de-DE' => '<p>Sie können die Artikel innerhalb von 48 Stunden herunterladen.</p><p>[filedownload]</p>'

			)
	);

	function wooTranslate($word,$language){
		$transltion = $this->translations[$word][$language];
		if(!$transltion)
			return $this->translations[$word]['en-US'];
		else
			return $transltion;
	}

	function getCodingKey(){
		return $this->coding_key;
	}
	function getDownloadTimetout(){
		return $this->download_timeout;
	}
}

?>
