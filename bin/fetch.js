const https = require("https");
const fs = require("fs");
const path = require("path");
const unzipper = require("unzipper");

(async () => {
  const IANSEO_URL = "https://ianseo.net";
  let html = null;

  const getHtml = () => {
    https
      .get(`${IANSEO_URL}/Releases.php`, (res) => {
        let data = [];
        console.log("Status Code:", res.statusCode);

        res.on("data", (chunk) => {
          data.push(chunk);
        });

        res.on("end", async () => {
          html = Buffer.concat(data).toString();
          const fileName = getFilename(html);
          console.log(fileName);
          if (fileName === null) {
            throw new Error("No file found");
          }
          await downloadFile(fileName);
          try {
            await unzipFile(
              path.join(__dirname, "..", fileName),
              path.join(__dirname, "..", ".tmp")
            );
          } catch (err) {
            throw new Error(err);
          }
        });
      })
      .on("error", (err) => {
        console.log("Error: ", err.message);
      });
  };

  const getFilename = (html) => {
    const regex = /Ianseo_\d{8}\.zip/g;
    const match = regex.exec(html);
    if (match.length === 0) {
      return null;
    }
    return match[0];
  };

  const downloadFile = async (fileName) => {
    const url = `${IANSEO_URL}/Release/${fileName}`;
    const filePath = path.join(__dirname, "..", fileName);

    return new Promise((resolve, reject) => {
      const file = fs.createWriteStream(filePath);
      https
        .get(url, (response) => {
          if (response.statusCode !== 200) {
            reject(
              new Error(`Failed to get '${url}' (${response.statusCode})`)
            );
            return;
          }

          response.pipe(file);

          file.on("finish", () => {
            file.close(resolve);
          });

          file.on("error", (err) => {
            fs.unlink(filePath, () => reject(err));
          });
        })
        .on("error", (err) => {
          fs.unlink(filePath, () => reject(err));
        });
    });
  };

  const unzipFile = async (zipFilePath, outputDir) => {
    return new Promise((resolve, reject) => {
      fs.createReadStream(zipFilePath)
        .pipe(unzipper.Extract({ path: outputDir }))
        .on("close", resolve)
        .on("error", reject);
    });
  };

  getHtml();
})();
