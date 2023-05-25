<?php echo $this->include("template/headerkosong_v"); ?>

<div class='container-fluid p-0'>
	<div class='row'>
		<div class='col'>
			<style>
            html{background-image: url("<?=base_url("images/room.png");?>");background-size: cover;}
            body{background:none;}

            .badge{border-radius:0px!important;}
            .category_name{font-size:60px!important; padding:10px; font-weight:bold;}
            #room{
                padding: 100px 50px 30px 50px !important;
            }
			.room{
				height:100px; 
				background:url("<?=base_url("images/room.jpg");?>");
				background-repeat: no-repeat;
				background-size: cover;
			}
			.inherit{width: inherit !important; height: inherit !important;}
			.carddeck{position: absolute !important; top: 40%; left:50%; transform:translate(-50%,-50%);}
			.carddeckbg{position: relative !important; top: 50%; left:50%; transform:translate(-50%,-50%);  opacity: 0.5;}
			.text{ background:rgba(255,255,255,0.7);}
			.judul{font-weight:bold; font-size:25px;}
			.subjudul1{font-weight:bold; font-size:12px; text-shadow:white 1px 1px 1px;}
			.subjudul2{font-size:12px;}
			.h25{height: 25px; }
			.h50{height: 50px; }
			.h100{height: 100px; }
			</style>
			<div class="row" id="room"></div>
			<script>
				function room(){
					$.get("<?=base_url("droom");?>")
					.done(function(data){
						$("#room").html(data);
					});
				}
				room();
				function roomstatus(product_id,product_status){
					$.get("<?=base_url("droomstatus");?>",{product_id:product_id,product_status:product_status})
					.done(function(data){
						room();
					});
				}
			</script>
		</div>
	</div>
</div>

<?php echo  $this->include("template/footersaja_v"); ?>

<?php //echo $this->endSection(); 
?>