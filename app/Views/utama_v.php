<?php echo $this->include("template/header_v"); ?>

<div class='container'>
	<div class='row'>
		<div class='col'>
			<div class="row">
				<!-- Column -->
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<div class="card-two">
								<header>
									<div class="avatar">
										<img src="images/global/user.png" alt="<?= session()->get("user_name"); ?>" />
									</div>
								</header>
								<h3><?= session()->get("position_name"); ?></h3>
								<div class="desc">
									<?= session()->get("store_name"); ?>
								</div>
								<!-- <div class="contacts"><?= session()->get("position_name"); ?>
									<div class="clear"></div>
								</div> -->
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--
			<div class="row">
				<?php
				$from = date("Y-m-01");
				$to = date("Y-m-t");
				$builder = $this->db
					->table("transaction");
				$builder->where("transaction.transaction_date >=", $from);
				$builder->where("transaction.transaction_date <=", $to);
				$builder->where("transaction.transaction_status", "2");//Pending
				$builder->where("transaction.store_id", session()->store_id);
				$pending = $builder
					->get()->getNumRows();
				// echo $this->db->getLastquery();
				?>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-title">
							<h4>Transaksi Pending Bulan Ini</h4>
						</div>
						<div class="todo-list">
							<div class="tdl-holder">
								<div class="tdl-content">
									<ul>
										<li class="color-primary">
											<label>
												<i class="bg-primary"></i><span><?= $pending; ?></span>
											</label>
										</li>
										<li class="color-warning">
											<label>
												<i class="bg-warning"></i><span></span>
											</label>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				$from = date("Y-m-01");
				$to = date("Y-m-t");
				$builder = $this->db
					->table("transaction");
				$builder->where("transaction.transaction_date >=", $from);
				$builder->where("transaction.transaction_date <=", $to);
				$builder->where("transaction.transaction_status", "0");//Sukses
				$builder->where("transaction.store_id", session()->store_id);
				$sukses = $builder
					->get()->getNumRows();
				// echo $this->db->getLastquery();
				?>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-title">
							<h4>Transaksi Sukses Bulan Ini</h4>
						</div>
						<div class="todo-list">
							<div class="tdl-holder">
								<div class="tdl-content">
									<ul>
										<li class="color-primary">
											<label>
												<i class="bg-primary"></i><span><?= $sukses; ?></span>
											</label>
										</li>
										<li class="color-warning">
											<label>
												<i class="bg-warning"></i><span></span>
											</label>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>	
				<?php
				$from = date("Y-m-01");
				$to = date("Y-m-t");
				$builder = $this->db
					->table("transaction");
				$builder->where("transaction.transaction_date >=", $from);
				$builder->where("transaction.transaction_date <=", $to);
				$builder->where("transaction.transaction_status", "1");//Batal
				$builder->where("transaction.store_id", session()->store_id);
				$batal = $builder
					->get()->getNumRows();
				// echo $this->db->getLastquery();
				?>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-title">
							<h4>Transaksi Batal Bulan Ini</h4>
						</div>
						<div class="todo-list">
							<div class="tdl-holder">
								<div class="tdl-content">
									<ul>
										<li class="color-primary">
											<label>
												<i class="bg-primary"></i><span><?= $batal; ?></span>
											</label>
										</li>
										<li class="color-warning">
											<label>
												<i class="bg-warning"></i><span></span>
											</label>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>			
			</div>
-->
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-title">
							<h4>Daftar Stock Limited</h4>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover ">
									<thead>
										<tr>
											<th>Product</th>
											<th>Jumlah</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$builder = $this->db
											->table("product");
										$builder->where("product_stock<=product_countlimit");
										$usr = $builder
											->where("store_id", session()->store_id)
											->where("product_type", "0")
											->orderBy("product_name","ASC")
											->get();
										// echo $this->db->getLastquery();
										foreach ($usr->getResult() as $usr) {
										?>
											<tr>
												<td><?= $usr->product_name; ?></td>
												<td><?= $usr->product_stock; ?></td>
											</tr>
										<?php }	?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<style>
			.room{
				height:200px; 
				background:url("<?=base_url("images/room.jpg");?>");
				background-repeat: no-repeat;
				background-size: cover;
			}
			.inherit{width: inherit !important; height: inherit !important;}
			.carddeck{position: absolute !important; top: 40%; left:50%; transform:translate(-50%,-50%);}
			.carddeckbg{position: relative !important; top: 50%; left:50%; transform:translate(-50%,-50%);  opacity: 0.5;}
			.text{ background:rgba(255,255,255,0.7);}
			.judul{font-weight:bold; font-size:13px;}
			.subjudul1{font-weight:bold; font-size:12px; text-shadow:white 1px 1px 1px;}
			.subjudul2{font-size:12px;}
			.h25{height: 25px; }
			.h50{height: 50px; }
			.h100{height: 100px; }
			</style>
			<div class="row" id="room">
				<?php ?>
			</div>
			<script>
				function room(){
					$.get("<?=base_url("room");?>")
					.done(function(data){
						$("#room").html(data);
					});
				}
				room();
				function roomstatus(product_id,product_status){
					$.get("<?=base_url("roomstatus");?>",{product_id:product_id,product_status:product_status})
					.done(function(data){
						room();
					});
				}
			</script>
		</div>
	</div>
</div>

<?php echo  $this->include("template/footer_v"); ?>

<?php //echo $this->endSection(); 
?>