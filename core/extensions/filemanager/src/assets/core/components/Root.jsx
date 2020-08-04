import React, { useState, useEffect } from "react";
import {
  Box,
  Row,
  Col,
  InputFile,
  Alert,
  FloatLeft,
  FloatRight,
  fetchData,
  Loader,
  UploadProgress,
  postData,
} from "./utils.jsx";

import Table from "./Table.jsx";

const Root = () => {
  // url 
  let api_url = `${site_url}/api/filemanager/all`;
  let upload_url = `${site_url}/filemanager/upload`;

  const [data, setData] = useState([]);
  const [IsLoading, setIsLoading] = useState(false);

  useEffect(() => {
    fetchData(api_url).then((r) => {
      setData(r);
      setIsLoading(true);
    });
  }, []);

  // upload file
  const handleUpload = (evt) => {
    let data = new FormData();
    data.append("file", fileElem.files[0]);
    postData(upload_url, data, filelabel, fileprogress)
      .then((res) => {
        if (res.status) {
          message(res.message);
          fetchData(api_url).then((r) => {
            setData(r);
            filelabel.textContent = "Upload";
            fileprogress.style.width = "0%";
          });
        } else {
          filelabel.textContent = "Upload";
          fileprogress.style.width = "0%";
          message("Error on upload file");
        }
      })
      .catch((errr) => message(err));
  };

  // search files
  const handleSearch = (evt) => {
    let key = evt.target.value;
    let filter, table, tr, td, i;
    filter = key.toUpperCase();
    table = document.querySelector("table");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[1];
      if (td) {
        if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  };

  // filter by select option
  const handleChange = (evt) => {
    let key = evt.target.value;
    let url = `${site_url}/api/filemanager/${key}`;
    setIsLoading(false);
    fetchData(url).then((r) => {
      console.log(r);
      setData(r);
      setIsLoading(true);
    });
  };

  return (
    <Box title="Filemanager" icon="toolbox">
      <Row>
        <Col num="12">
          <FloatLeft m="3">
            <InputFile id="fileElem" labelId="filelabel" fn={handleUpload} />

            <a
              href={`${site_url}/api/filemanager/all`}
              target="_blank"
              className="btn btn-primary"
            >
              <i className="fa fa-code"></i>
            </a>
          </FloatLeft>
          <FloatRight m="3">
            <Alert type="info">
              <b>Note: </b> You can compress images with{" "}
              <a href="https://imagecompressor.com/">image optimizer</a>
            </Alert>
          </FloatRight>
        </Col>
      </Row>
      <Row>
        <Col num="12">

          {/* upload progress */}
          <UploadProgress />

          {/* search input and select */}
          <form className="form-inline ml-2 mb-3">
            <div className="form-group">
              <input
                className="form-control"
                type="search"
                placeholder="Search"
                onKeyUp={handleSearch}
              />
            </div>
            <div className="form-group">
              <select className="form-control" onChange={handleChange}>
                <option value="all"> -- </option>
                <option value="images">Images</option>
                <option value="videos">Videos</option>
                <option value="audio">Audio</option>
                <option value="docs">Documents</option>
                <option value="other">Other</option>
              </select>
            </div>
          </form>

          {/* show data tables */}
          {IsLoading ? (
            <Table data={data} />
          ) : (
            <Loader />
          )}
        </Col>
      </Row>
    </Box>
  );
};

export default Root;
