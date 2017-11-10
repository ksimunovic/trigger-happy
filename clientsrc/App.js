import React, { Component } from 'react';
import { Provider } from 'react-redux'
import { createStore, applyMiddleware, compose } from 'redux'
import store from '../jsclient/store/';
import  { loadDataType} from '../jsclient/actions';
import {parseExpression, stringifyExpression} from '../jsclient/lib/util';
import {fetchPlugins, setup} from '../jsclient/actions/';
import thunk from 'redux-thunk';


// import ReactNodeGraph from 'react-node-graph';

import ReactNodeGraph from '../jsclient/';

export default class App extends Component {

  constructor(props) {
    super(props);
    this.state = {errors: "" };
    let graph = this.stringifyExpressionsInGraph(this.props.graph);

    const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;
    let loadedNodes = graph.nodes;
    this.state = graph;

    if (!window.nodeStore) {
         window.nodeStore = createStore(store,{ fieldTypes: graph.fieldTypes, datatypes: {
             'boolean': { base: null, choices:[{ id: false, text: "No" },{ id: true, text: "Yes" }], ajax: false },
             "string":{ base: null, schema: {  methods: { "toUpperCase": {
                     description: "To Upper Case",
                     "type":"string",
                     'fields': {

                     }
                 }, "toLowerCase": {
                     description: "To Lower Case",
                     "type":"string",
                     'fields': {

                     }
                 }}}},
                 "array":{ base: null, schema: {methods: { "getItem": {
                     description: "Get Item",
                     "type":"number",
                     'fields': {
                         'key': {
                             'type':"string",
                             "description":"The item key"
                         }
                     }
                 }}}}
             }},composeEnhancers(applyMiddleware(thunk)));

        window.nodeStore.dispatch(fetchPlugins());
    }
    this.nodeStore = window.nodeStore;
    //this.nodeStore.dispatch(setup());
    window.sss = this.nodeStore;
    var self = this;
    window.app = this;
    window.ssssApp = function() {

            window.nodes = self.nodeStore.getState().nodes;
            window.defs = self.nodeStore.getState().definitions;

        return JSON.stringify({

            nodes: self.getNodesForSave(self.nodeStore.getState().nodes,self.nodeStore.getState().definitions)
        })
    };
    window.nodeStore1 = this.nodeStore;

    this.saveGraph();
  }
  stringifyExpressionsInGraph(graph) {
      for (var g in graph.nodes) {
          for (var e in graph.nodes[g].expressions) {
              let expr = graph.nodes[g].expressions[e];
              graph.nodes[g].expressions[e] = stringifyExpression(expr);
          }
          graph.nodes[g].filters = this.stringifyFilters(graph.nodes[g].filters);
      }
      return graph;
  }
  onInputValueUpdated(nId,inputIndex, value) {
      let nodes = this.state.nodes;

      let matches = nodes.filter(n=>n.nid == nId);
      if (matches.length) {
          matches[0].fields.in[inputIndex].value = value;
      }
      this.setState({nodes:nodes});
      this.forceUpdate();
  }
  onNodeInsert(node,nodeType, x, y, fromNode, fromPin) {
      var exampleGraph = this.state;
      var nId = this.state.nodes.reduce(function(acc,next) { return next.nid > acc ? next.nid : acc; },0) + 1;

      let nodeToInsert = Object.assign({},node);
      nodeToInsert.nid = nId;
      nodeToInsert.x = x;
      nodeToInsert.y = y;
      exampleGraph.nodes.push(nodeToInsert);

      this.setState(exampleGraph);

      if (fromNode && fromPin)
      this.onNewConnector(fromNode,fromPin,nId,nodeToInsert.fields.in[0].name);

  }
  onNewConnector(fromNode,fromPin,toNode,toPin) {
      //.filter(r=>r.type == 'flow' || (r.to_node != toNode || r.to != toPin))
    let connections = [...this.state.connections, {
      from_node : fromNode,
      from : fromPin,
      to_node : toNode,
      to : toPin
    }]

    this.setState({connections: connections})
  }
  ensureNodesLinked(nodes) {
  }
  parseExpression(nodeId, fieldName, expression,fieldType) {


      let oType = fieldType;

      while  (fieldType !== 'number' &&fieldType !== 'boolean' && fieldType !== 'string' && fieldType !== 'html')
      {
          if (!this.nodeStore.getState().datatypes[fieldType]) {
            this.nodeStore.dispatch(loadDataType(fieldType));
            break;
          }
          else {
              if (!this.nodeStore.getState().datatypes[fieldType].base)
                break;
              fieldType = this.nodeStore.getState().datatypes[fieldType].base;

          }
      }


      if (fieldType !== 'string' &&fieldType !== 'boolean' && fieldType !== 'html') {
          if (fieldType == "number") {
              if (!isNaN(parseFloat(expression)) && isFinite(expression))
              return expression;
          }
          return parseExpression(expression, nodeId, fieldName, fieldType, nodeStore);
      } else {
          return expression;
      }
  }
  parseExpressions(nodeId, expressions) {
      if (!expressions)
        return null;
      let parsedExpressions = {};
      let fieldType = null;

      for (var i in expressions) {
          var expression = expressions[i];

          if (this.nodeStore.getState().nodes[nodeId].fields) {
              for (var f in this.nodeStore.getState().nodes[nodeId].fields) {
                  if (this.nodeStore.getState().nodes[nodeId].fields[f].name == i) {
                      // Got a match
                      fieldType = this.nodeStore.getState().nodes[nodeId].fields[f].type;
                      break;
                  }
              }
          }

          if (fieldType) {
              let existingType = this.nodeStore.getState().fieldTypes[nodeId + "." + i];
              //if (existingType && (fieldType == 'any' || fieldType == '@any'))
            //    fieldType = existingType;
              if (fieldType.indexOf("$") === 0) {
                  fieldType = this.nodeStore.getState().fieldTypes[nodeId + "." + fieldType.substring(1)];
              }

          }

          parsedExpressions[i] =this.parseExpression(nodeId, i,expression,fieldType);

         // console.log(fieldType,this.nodeStore.getState().datatypes);
      }

      return parsedExpressions;
  }

  parseFilterExpressions(nodeId, filterGroups) {
      if (!filterGroups)
        return null;
      let parseFilters = [];
      let fieldType = null;
      for (var i in filterGroups)
      {
          parseFilters[i] = [];
          for (var j in filterGroups[i]) {
              var newFilter = {};
              newFilter.op = filterGroups[i][j].op;
              if (!filterGroups[i][j].left || !filterGroups[i][j].left.expr)
                continue;
              if (filterGroups[i][j].left.expr.type) {
                  newFilter.left = filterGroups[i][j].left.expr;
              } else {

                  newFilter.left = { expr: this.parseExpression(nodeId, "",filterGroups[i][j].left.expr,'object'), display: filterGroups[i][j].left.display };
              }
              newFilter.right = filterGroups[i][j].right;
              parseFilters[i].push(newFilter);
          }
      }
      return parseFilters;
  }
  stringifyFilters( filterGroups) {
      if (!filterGroups)
        return null;
      let parseFilters = [];
      let fieldType = null;
      for (var i in filterGroups)
      {
          parseFilters[i] = [];
          for (var j in filterGroups[i]) {
              var newFilter = {};
              newFilter.op = filterGroups[i][j].op;

                  newFilter.left = { expr: stringifyExpression(filterGroups[i][j].left.expr), display: filterGroups[i][j].left.display };

              newFilter.right = filterGroups[i][j].right;
              parseFilters[i].push(newFilter);
          }
      }
      return parseFilters;
  }

  getNodesForSave(nodes,defs) {
      var prevNode = null;
      var newNodes = Object.values(nodes).map((n)=> {
          let def = defs && defs[n.type] || false;
          var newNode = {
              nid: n.nid,
              x: n.x,
              y: n.y,
              type: n.type,
              expressions: this.parseExpressions(n.nid, n.expressions),
              filters: this.parseFilterExpressions(n.nid, n.filters),
          };
          return newNode;
      });
      let actionOrTriggerNodes = newNodes.filter(n=>defs && n.type && defs[n.type] && (defs[n.type].nodeType == "action" ||defs[n.type].nodeType == "condition" || defs[n.type].nodeType == "trigger") );
      for( var i =1; i < actionOrTriggerNodes.length; i++) {

          actionOrTriggerNodes[i-1].next = [actionOrTriggerNodes[i].nid];
      }
      return newNodes;
  }

  getConnectionsForSave() {
      return this.state.connections;
  }
  saveGraph() {
      var self = this;

      this.props.linkedField.value = (JSON.stringify({
          nodes: self.getNodesForSave(self.nodeStore.getState().nodes,self.nodeStore.getState().definitions),
          fieldTypes: self.nodeStore.getState().fieldTypes
      }));
  }
  componentDidUpdate(prevProps, prevState) {

  }

  componentDidMount() {


      this.nodeStore.subscribe(this.saveGraph.bind(this));
  }
  onRemoveConnector(connector) {
    let connections = [...this.state.connections]
    connections = connections.filter((connection) => {
      return connection != connector
    })

    this.setState({connections: connections})
  }
    onNodeDelete(nid) {
        var exampleGraph = this.state;
        var nodes = Object.values(this.state.nodes).filter(n=>n.nid != nid);
        var connections = this.state.connections.filter(n=>n.from_node != nid && n.to_node != nid);
        exampleGraph.nodes = nodes;
        exampleGraph.connections = connections;
        this.setState(exampleGraph);
    }
  onNodeMove(nid, pos) {
    console.log('end move : ' + nid, pos)
  }

  onNodeStartMove(nid) {
    console.log('start move : ' + nid)
  }

  handleNodeSelect(nid) {
    console.log('node selected : ' + nid)
  }

  handleNodeDeselect(nid) {
    console.log('node deselected : ' + nid)
  }

  render() {

      return (


          <Provider store={this.nodeStore}>

          <ReactNodeGraph
            graph={this.state}
            errors={this.state.errors}
            onChange={this.saveGraph.bind(this)}
            onInputValueUpdated={this.onInputValueUpdated.bind(this)}
            onNodeInsert={this.onNodeInsert.bind(this)}
            onNodeMove={(nid, pos)=>this.onNodeMove(nid, pos)}
            onNodeDelete={(nid)=>this.onNodeDelete(nid)}
            onNodeStartMove={(nid)=>this.onNodeStartMove(nid)}
            onNewConnector={(n1,o,n2,i)=>this.onNewConnector(n1,o,n2,i)}
            onRemoveConnector={(connector)=>this.onRemoveConnector(connector)}
            onNodeSelect={(nid) => {this.handleNodeSelect(nid)}}
            onNodeDeselect={(nid) => {this.handleNodeDeselect(nid)}}
          />
          </Provider>

      );
  }
}
