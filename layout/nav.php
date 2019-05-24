<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_Genre.php';
require_once INCLUDE_PATH.DS. 'C_User.php';

$amp_genres = Genre::get_genres_by_category('Amp');
$cab_genres = Genre::get_genres_by_category('Cab');

?>

<nav id="main_nav">
	<ul id="main_menu" class="vertical_menu">
		<li><a href="index.php">Home</a></li>
		<li><a href="about_amp_warz.php">About</a></li>
		<li><a href="vote.php">Vote!</a></li>
		<li>
			View Ranks
			<ul>
				<li>
					Amps
					<ul>
						<?php foreach ($amp_genres as $genre) { ?>
						<li><a href="view_ranks.php?gen_id=<?php echo $genre->id; ?>"><?php echo $genre->name; ?></a></li>
						<?php } ?>
					</ul>
				</li>
				<li>
					Cabs
					<ul>
						<?php foreach ($cab_genres as $genre) { ?>
						<li><a href="view_ranks.php?gen_id=<?php echo $genre->id; ?>"><?php echo $genre->name; ?></a></li>
						<?php } ?>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			Contribute
			<ul>
				<li>
					Amps
					<ul>
						<?php foreach ($amp_genres as $genre) { ?>
						<li><a href="contribute_amp.php?gen_id=<?php echo $genre->id; ?>"><?php echo $genre->name; ?></a></li>
						<?php } ?>
					</ul>
				</li>
				<li>
					Cabs
					<ul>
						<?php foreach ($cab_genres as $genre) { ?>
						<li><a href="contribute_cab.php?gen_id=<?php echo $genre->id; ?>"><?php echo $genre->name; ?></a></li>
						<?php } ?>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			<?php if ($logged_in_user) { ?>
			<a href="logout.php">Log Out</a>
			<?php } else { ?>
			<a href="login.php">Log In</a>
			<?php } ?>
		</li>
	</ul>
</nav>