import React, { useState, useEffect } from "react";
import axios from "axios";
import { useParams, useHistory } from "react-router-dom";
import { Box, Row, Col, renderIconType } from "./utils.jsx";

const Rename = () => {
  let history = useHistory();
  let params = useParams();

  const [name, setName] = useState('');

  useEffect(() => setName(params.name),[])

  const renameFile = async () => {

    let oldName = params.name
    let newName = name
    
    let url = `${site_url}/filemanager/rename`
    let data = {
        "old": oldName,
        "new": newName,
        "ext": params.ext
    }
    const response = await axios.post(url, data);
    const result = await response.data;

    // if true show msg and redirect
    if(result.status){
        message(result.msg);
        let w = setTimeout(() => {
            history.push('/');
            clearTimeout(w);
        },2000);
    }else{
        message(result.msg);
    }
  };

  return (
    <Box title={`Edit file ${params.name}`} icon="edit">
      <Row>
        <Col num="12">
          <button
            className="btn btn-danger m-1"
            onClick={() => history.push("/")}
          >
            <i className="fa fa-arrow-left mr-2"></i>
            Back
          </button>
        </Col>
      </Row>
      <Row>
        <Col num="6">
          <div className="bg-light text-center m-1 p-3">
            {renderIconType(params.ext)}
            <p>
              <small>
                {name}.{params.ext}
              </small>
            </p>
          </div>
          <div className="form-inline m-1 p-1">
            <div className="form-group">
              <input
                type="text"
                className="form-control"
                value={name}
                onChange={(evt) => setName(evt.target.value.replace(/ /g, "-"))}
              />
            </div>
            <div className="form-group">
              <button className="btn btn-primary" onClick={() => renameFile()}>
                <i className="fa fa-edit mr-2"></i>
                Rename
              </button>
            </div>
          </div>
        </Col>
      </Row>
    </Box>
  );
};

export default Rename;
