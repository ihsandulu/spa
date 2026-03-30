<?php echo $this->include("template/headerkosong_v"); ?>
<div class='container-fluid p-0'>
	<div class='row'>
		<div class='col'>
			<style>
				html {
					background-image: url("<?= base_url("images/room.png"); ?>");
					background-size: cover;
				}

				body {
					background: none;
				}

				.badge {
					border-radius: 0px !important;
				}

				.category_name {
					font-size: 60px !important;
					padding: 10px;
					font-weight: bold;
				}

				#room {
					padding: 100px 50px 30px 50px !important;
				}

				.room {
					height: 200px;
					background: url("<?= base_url("images/room.jpg"); ?>");
					background-repeat: no-repeat;
					background-size: cover;
				}

				.inherit {
					width: inherit !important;
					height: inherit !important;
				}

				.carddeck {
					position: absolute !important;
					top: 40%;
					left: 50%;
					transform: translate(-50%, -50%);
				}

				.carddeckbg {
					position: relative !important;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					opacity: 0.5;
				}

				.text {
					background: rgba(255, 255, 255, 0.7);
				}

				.judul {
					font-weight: bold;
					font-size: 18px;
					padding: 0px;
				}

				.subjudul1 {
					font-weight: bold;
					font-size: 12px;
					text-shadow: white 1px 1px 1px;
				}

				.subjudul2 {
					font-size: 12px;
				}

				.h25 {
					height: 25px;
				}

				.h50 {
					height: 50px;
				}

				.h100 {
					height: 100px;
				}
			</style>
			<div class="row" id="room"></div>
			<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
			<script>
				const socket = io("http://localhost:3000");

				let roomsData = {};

				// =======================
				// 🔥 HITUNG STATUS (1 SUMBER)
				// =======================
				function hitung(room) {
					const now = Date.now();
					const start = new Date(room.product_start).getTime();
					const bend = new Date(room.product_bend).getTime();
					const end = new Date(room.product_end).getTime();

					let sisa = Math.floor((end - now) / 1000);
					if (sisa < 0) sisa = 0;

					let status = "secondary";
					let nstatus = "KOSONG";
					let bg = "";
					let show = false;

					if (room.product_status == 0 && room.transaction_id > 0) {

						if (now >= start && now < bend) {
							status = "success";
							nstatus = "TERISI";
							bg = "#ddf3e4";
							show = true;

						} else if (now >= bend && now <= end) {
							status = "warning";
							nstatus = "AKAN HABIS";
							bg = "#f9f7e1";
							show = true;

						} else if (now > end && now <= end + 10 * 60 * 1000) {
							status = "danger";
							nstatus = "SELESAI";
							bg = "#f7d7d7";
							show = true;
						}

					} else if (room.product_status == 1) {
						status = "light";
						nstatus = "DIBERSIHKAN";

					} else if (room.product_status == 2) {
						status = "dark";
						nstatus = "RUSAK";
					}

					return {
						sisa,
						status,
						nstatus,
						bg,
						show
					};
				}

				// =======================
				// 🔥 RENDER 1X
				// =======================
				function render() {
					let html = "";

					Object.values(roomsData).forEach(room => {
						const r = hitung(room);

						html += `
        <div class="col-lg-2 p-2 rounded">
            <div class="room rounded">
                <div class="carddeckbg bg-${r.status} inherit rounded"></div>

                <div class="carddeck rounded"><br/>
                    <div class="text p-2 text-center row">

                        <div class="judul col-12">${room.product_name}</div>
                        <div class="subjudul1 text-${r.status} col-12">${room.customer_name || ''}</div>

                        <div class="col-12 d-grid">

                            <button class="btn btn-sm btn-${r.status}">
                                ${r.nstatus}
                            </button>

                            ${
                                r.show
                                ? `<div id="sisa-${room.product_id}" style="margin-top:10px;background:${r.bg};padding:5px;">
                                    ${format(r.sisa)}
                                   </div>`
                                : ""
                            }

                        </div>
                    </div>
                </div>
            </div>
        </div>`;
					});

					document.getElementById("room").innerHTML = html;
				}

				// =======================
				// 🔥 SOCKET MASUK
				// =======================
				socket.on("rooms", rooms => {
					roomsData = {};
					rooms.forEach(r => roomsData[r.product_id] = r);
					render(); // render awal
				});

				// =======================
				// 🔥 COUNTDOWN GLOBAL
				// =======================
				setInterval(() => {
					Object.values(roomsData).forEach(room => {
						const r = hitung(room);

						const el = document.getElementById(`sisa-${room.product_id}`);
						if (el) el.innerText = format(r.sisa);

						const card = el?.closest(".room");
						if (card) {
							card.querySelector(".carddeckbg").className = `carddeckbg bg-${r.status} inherit rounded`;
							const btn = card.querySelector("button");
							btn.className = `btn btn-sm btn-${r.status}`;
							btn.innerText = r.nstatus;
						}
					});
				}, 1000);

				// =======================
				function format(sec) {
					let h = Math.floor(sec / 3600);
					let m = Math.floor((sec % 3600) / 60);
					let s = sec % 60;
					return `${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
				}
			</script>
		</div>
	</div>
</div> <?php echo $this->include("template/footersaja_v"); ?>
<?php //echo $this->endSection(); 
?>


