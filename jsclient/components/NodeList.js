import React from 'react';
import SimpleNode from './SimpleNode';
import { connect } from 'react-redux';

function renderAddPlaceholder(createNewClick) {
      return (<div onClick={createNewClick}>
      <section  className="node node--add-new" >
    <span className="button node-title button-large"><i className="fa fa-plus"></i> &nbsp; Add new Action</span>
  </section>
  </div>)

}
function isCurrentStep() { return false; }
function renderResultPlaceholder(label, navigate, allsteps) {
        let steps = allsteps.filter(r=>r.isReturn);

      return (<div>
      <section  className="node node--result" >
      <div className="node-icon node-icon--result">
        <i className="fa fa-stop-circle" />
      </div>
      <div className="node-details">
      <header className="node-header">

        <div className="node-header__top">

        <span className="node-title" >
        {label}
        </span>

        </div>


      </header>
      { steps && steps.map(step=>{
          return (<a onClick={()=>navigate(step.page,step.options)} href="javascript:void(0)" className={'node-menu__item ' + (isCurrentStep(step) ? 'node-menu__item--selected': '')}>
            <i className={"fa "+ step.icon}></i> {step.text}
          </a>);
      })}


      </div>
  </section>
  </div>)

}
function renderNode(node,i, onSelect, onTest,fetchNode, steps) {
    let totalOutputNonFlowPins = (node.fields.out||[]).filter(p=>p.type !== 'flow').length;
    let totalInputNonFlowPins = (node.fields.in||[]).filter(p=>p.type !== 'flow').length;
    let autocollapse = (totalInputNonFlowPins == 0 && totalOutputNonFlowPins == 0);

    return (<SimpleNode
    index={i}
    nodeId={node.nid}
    fetchNode={fetchNode}
    selectNode={(nid,type)=>onSelect(nid,type)}
    testNode={(nid)=>onTest(nid)}
                    index={i++}
                    nid={node.nid}
                    color="#000000"
                    title={node.type}
                    inputs={node.fields.in}
                    outputs={node.fields.out}
                    pos={{x : node.x, y: node.y}}
                    key={node.nid}
                    node={node}

                    />);
}

const NodeList = ({dataNodes,fetchNode, navigate, nodeDefinitions,triggerNodes, actionNodes, resultNode, onNodeEdit, onNodeTest,  showCreateNew, steps}) => {
    resultNode = false;
    return (<div className="node-list">
    <div className="node-wrapper">
    {triggerNodes && triggerNodes.map((node,i) => renderNode(node,i, onNodeEdit, onNodeTest, fetchNode, steps))}
    {actionNodes && actionNodes.map((node,i) => renderNode(node,i, onNodeEdit, onNodeTest,  fetchNode, steps))}
    </div>
    { renderAddPlaceholder(showCreateNew)}
    {resultNode && renderResultPlaceholder(resultNode.resultLabel,navigate, steps[resultNode.nid] || [])}

 </div>);};

 const mapStateToProps = state => {
     var defs = state.definitions;

     var resultNodes = Object.values(state.nodes).filter(n=>defs && defs[n.type] && defs[n.type].resultLabel && defs[n.type].resultLabel !== "")
     .map(n=>Object.assign({ nodeType: "result" },n, state.definitions[n.type],{ nodeType: "result", name: defs[n.type].resultLabel, description: defs[n.type].resultDesc } ));
      return {
          steps: state.steps,
          fetchNode: (nid) => state.nodes.filter(n=>n.nid == nid).pop(),
         triggerNodes: Object.values(state.nodes).map(n=>Object.assign({},n, state.definitions[n.type] )).filter(n=> n.nodeType == "trigger"),
         actionNodes: Object.values(state.nodes).map(n=>Object.assign({},n, state.definitions[n.type] )).filter(n=> n.nodeType == "action" || n.nodeType == 'condition'),
         dataNodes: Object.values(state.nodes).map(n=>Object.assign({},n, state.definitions[n.type] )).filter(n=>n.nodeType == "data"),
         resultNode: (resultNodes.length > 0 ? resultNodes[0] : null)
     };
 };

 const mapDispatchToProps = dispatch => {
   return {
             navigate: (step, options)=>dispatch(navigate(step,options)),
       onNodeEdit: (id,type) => {

           dispatch(editNode(id,type))
       },onNodeTest: id => {

           dispatch(testNode(id))
       },
       showCreateNew: ()=> { return dispatch(navigate('CreateNew')); }

   }
 }

 export default  connect(
   mapStateToProps,
   mapDispatchToProps
 )(NodeList)
