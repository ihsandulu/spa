// socket-server.js (revisi)
const mysql = require("mysql2");
const { Server } = require("socket.io");
const http = require("http");

const server = http.createServer();
const io = new Server(server, {
    cors: { origin: "*" }
});

const db = mysql.createPool({
    host: "localhost",
    user: "root",
    password: "",
    database: "spa2",
    port: 3307
});

function query(sql, params) {
    return new Promise((resolve, reject) => {
        db.query(sql, params, (err, results) => {
            if (err) reject(err);
            else resolve(results);
        });
    });
}

io.on("connection", socket => {
    console.log("Client connected:", socket.id);

    sendRooms(socket);

    const interval = setInterval(() => sendRooms(socket), 2000);

    socket.on("disconnect", () => {
        console.log("Client disconnected:", socket.id);
        clearInterval(interval);
    });
});

async function sendRooms(socket) {
    try {
        // query persis PHP
        const sql = `
            SELECT p.*, c.category_id, c.category_name,
                   (SELECT product_name FROM product WHERE product_lanjutan = p.product_id LIMIT 1) AS pname
            FROM product p
            LEFT JOIN category c ON c.category_id = p.category_id
            WHERE p.category_id = 100 AND p.product_lanjutan = 0
            ORDER BY p.product_urutan ASC
        `;
        const rooms = await query(sql);
        socket.emit("rooms", rooms);
    } catch (err) {
        console.error(err);
    }
}

server.listen(3000, () => console.log("Socket.IO server running on port 3000"));