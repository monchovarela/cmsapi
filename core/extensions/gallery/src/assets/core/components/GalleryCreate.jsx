import React, { Fragment, useState, useEffect } from "react";
import { Link, useHistory } from "react-router-dom";

import { UploadProgress, postData, Loader, Row, Col } from "./utils.jsx";

const GalleryCreate = () => {
  let create_url = `${site_url}/gallery/create`;
  // use for redirect
  const history = useHistory();
  // initial data
  const [loaded] = useState(true);
  const [title, setTitle] = useState(undefined);
  const [description, setDescription] = useState(undefined);
  const [template, setTemplate] = useState(undefined);
  const [poster, setPoster] = useState(undefined);
  // update data
  const handleSubmit = (evt) => {
    evt.preventDefault();
    let create_data = {
      title: title,
      description: description,
      template: template,
      poster: poster,
    };
    postData(create_url, create_data, createBtn, fileprogress).then((r) => {
      message(r.message);
      createBtn.textContent = "Save";
      fileprogress.style.width = "0%";
      let w = setTimeout(() => {
        history.push("/");
        clearTimeout(w);
      }, 1000);
    });
    return false;
  };

  return loaded ? (
    <Fragment>
      <UploadProgress />
      <form onSubmit={handleSubmit}>
        <Row>
          <Col num="6">
            <div className="form-group">
              <label>Title</label>
              <input
                type="text"
                name="title"
                className="form-control"
                value={title || ""}
                onChange={(e) => setTitle(e.target.value)}
              />
            </div>
            <div className="form-group">
              <label>Description</label>
              <textarea
                name="description"
                className="form-control"
                rows="5"
                value={description || ""}
                onChange={(e) => setDescription(e.target.value)}
              />
            </div>
          </Col>
          <Col num="6">
            <div className="form-group">
              <label>Poster</label>
              <input
                type="text"
                name="poster"
                className="form-control"
                value={poster || ""}
                onChange={(e) => setPoster(e.target.value)}
              />
            </div>
            <div className="form-group">
              <label>Template</label>
              <select
                name="template"
                className="form-control"
                onChange={(e) => setTemplate(e.target.value)}
              >
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
            </div>
          </Col>
        </Row>
        <Row>
          <Col num="6">
            <button
              type="submit"
              id="createBtn"
              className="btn btn-primary mr-sm-3"
            >
              Save
            </button>
            <Link to="/" className="btn btn-warning">
              Cancel
            </Link>
          </Col>
        </Row>
      </form>
    </Fragment>
  ) : (
    <Loader />
  );
};

export default GalleryCreate;
