import React, { Fragment } from "react";
import axios from "axios";


/**
 * Render icon by type
 * @param {string} type 
 */
const renderIconType = (type) => {
  if (type === "mp3" || type === "wav" || type === "ogg")
    return <i className="fas fa-file-audio fa-2x text-secondary"></i>;
  if (type === "json" || type === "md")
    return <i className="fas fa-file-code fa-2x text-secondary"></i>;
  if (type === "css") return <i className="fab fa-css3 fa-2x text-secondary"></i>;
  if (type === "html") return <i className="fab fa-html5 fa-2x text-secondary"></i>;
  if (type === "js") return <i className="fab fa-js fa-2x text-secondary"></i>;
  if (type === "php") return <i className="fab fa-php fa-2x text-secondary"></i>;
  if (type === "py") return <i className="fab fa-python fa-2x text-secondary"></i>;
  if (
    type === "jpg" ||
    type === "jpeg" ||
    type === "png" ||
    type === "gif" ||
    type === "JPG" ||
    type === "JPEG"
  )
    return <i className="fas fa-image fa-2x text-secondary"></i>;
  if (type === "pdf")
    return <i className="fas fa-file-pdf fa-2x  text-secondary"></i>;
  if (type === "txt" || type === "docx")
    return <i className="fas fa-file-pdf fa-2x  text-secondary"></i>;
  if (type === "rar" || type === "zip" || type === "tar")
    return <i className="fas fa-file-archive fa-2x text-secondary"></i>;
  if (type === "mp4" || type === "webm" || type === "ogv")
    return <i className="fa fa-video fa-2x text-secondary"></i>;
  else return <i className="fas fa-file fa-2x text-secondary"></i>;
};

/**
 * Box Component
 * <Box icon="" title="" >content</Box>
 * 
 * @param {*} props 
 */
const Box = (props) => (
    <div className="container p-0">{props.children}</div>
);

/**
 * Alert Component
 * <Alert type="">content</Alert>
 * 
 * @param {*} props 
 */
const Alert = (props) => (
  <div className={`alert alert-${props.type || "info"}`}>{props.children}</div>
);

/**
 * Button Component
 * <Button type="" title="" fn="onclick method"/>
 * 
 * @param {*} props 
 */
const Button = (props) => (
  <button className={`btn btn-${props.type || "primary"} mr-3`} onClick={props.fn}>
    {props.title || 'Button'}
  </button>
);

/**
 * FloatLeft Component
 * <FloatLeft m="margin"></FloatLeft>
 * 
 * @param {*} props 
 */
const FloatLeft = (props) => (
  <div className={`float-left m-${props.m || "0"}`}>{props.children}</div>
);
/**
 * FloatRight Component
 * <FloatRight m="margin"></FloatRight>
 * 
 * @param {*} props 
 */
const FloatRight = (props) => (
  <div className={`float-right m-${props.m || "0"}`}>{props.children}</div>
);
/**
 * InputFile Component
 * <InputFile id="" fn="" title="" />
 * 
 * @param {*} props 
 */
const InputFile = (props) => (
  <Fragment>
    <input className="d-none" type="file" id={props.id} onChange={props.fn} />
    <label
      className={`d-inline btn btn-${props.color || "dark"} p-2 mr-3`}
      htmlFor={props.id}
    > 
      <i className="fas fa-upload mr-2"></i>
      <span id={props.labelId}>{props.title || "Upload file"}</span>
    </label>
  </Fragment>
);

/**
 * Row Component
 * <Row>content</Row>
 * 
 * @param {*} props 
 */
const Row = (props) => <div className="row">{props.children}</div>;

/**
 * Col Component
 * <Col num="">content</Col>
 * 
 * @param {*} props 
 */
const Col = (props) => (
  <div className={`col-md-${props.num} col-sm-12 mb-3`}>{props.children}</div>
);

/**
 * Loader Component
 * @param {*} props 
 */
const Loader = () => (
  <div className="preloader">
    <div className="lds-ripple">
      <div></div>
      <div></div>
    </div>
  </div>
);

/**
 * Loader Component
 * @param {*} props 
 */
const UploadProgress = () => (
  <div className="file-progress">
    <span id="fileprogress"></span>
  </div>
);

/**
 *  Get ajax data
 * 
 * @param {string} url
 */
const fetchData = async (url) => {
  const response = await axios.get(url);
  const result = await response.data;
  return result;
};

/**
 *  Post ajax data
 *
 * @param {string} url
 * @param {array} data
 * @param {string} label
 * @param {string} progress
 */
const postData = async (url, data, label, progress) => {
  const config = {
    onUploadProgress: function (progressEvent) {
      let percentCompleted = Math.round(
        (progressEvent.loaded * 100) / progressEvent.total
      );
      label.textContent = percentCompleted + "%";
      progress.style.width = percentCompleted + "%";
    },
  };
  const response = await axios.post(url, data, config);
  const result = await response.data;
  return result;
};

export {
  Box,
  Row,
  Col,
  Button,
  Alert,
  FloatLeft,
  FloatRight,
  InputFile,
  fetchData,
  postData,
  UploadProgress,
  Loader,
  renderIconType
};
