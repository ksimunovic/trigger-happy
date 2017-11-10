import React from 'react';
import jsep from 'jsep';

function getNodeFromStore(store,nid) {
	let nodes = store.nodes.filter(f=>f.nid == nid );
	if (nodes.length > 0)
		return nodes[0];
	return false;

}
function getValueFromVarName(store, varname, fromNode) {

	if (fromNode.values && fromNode.values.in) {
		let matches = fromNode.values.in.filter(f=>f.name == varname);
		if (matches.length > 0) {
			let matched = matches[0];
			let value = matched.value;
			if (Array.isArray(value)) {
				value = value.pop();
			}
			return value;
		}
	}
	return __buildExpression(store,fromNode,varname);
}
function checkNodeTag(fetchConnectingNode,name,prop) {

	if (name && prop) {
		let match = name.match(/_N([0-9]*)/);
		if (match != null) {
			let matchingNode = fetchConnectingNode(match[1]);
			return {
				node: matchingNode,
				pinId: prop.join('.'),
				nodeId: match[1],
				nodeLabel: matchingNode.type,
				pinLabel: prop.join('.')
			};
		}

	}
	return false;
}
function rebuildExpressionFromMustache(fetchConnectingNode,replaceExpressionNodeTag,val, isString) {
	if (!val)
	 return val;
	return val.replace(/{{(.*)}}/g,function(match,p1) {
		p1 = p1.replace('}}','');

		let matches = p1.match(/_N([0-9]*)\.(.*)/);
		if (matches != null) {
			// Is a node
			let expr = jsep(p1);

			return rebuildExpressionFromAST(fetchConnectingNode,replaceExpressionNodeTag,expr, true);
		}
		return p1;
	});
}

function getRootObjectFromMemberExpression(obj) {
	if (obj.type && obj.type == 'MemberExpression') {
		return getRootObjectFromMemberExpression(obj.object);
	}
	return obj;
}

function walkExpression(ast, callback) {
	if (ast == undefined)
		return;
	callback(ast);
	switch(ast.type) {
		case 'BinaryExpression':
			walkExpression(ast.left,callback);
			walkExpression(ast.right,callback);
			walkExpression(ast.operator,callback);
		case 'Literal':
			walkExpression(ast.value,callback);
		case 'Identifier':
			walkExpression(ast.name,callback);
		//case 'MemberExpression':
		//	walkExpression(ast.property,callback);
	//		walkExpression(ast.object,callback);
				return;
		//case 'CallExpression':
		//	var args = '';
		//	for (var i in ast.arguments) {
		//		walkExpression(ast.arguments[i],callback);

		//	}
		//	walkExpression(ast.callee,callback);
		//	xpression-data-tag'>" + rebuildExpressionFromAST(fetchConnectingNode,replaceExpressionNodeTag,ast.object) +" <span class='node-expression-data-tag__prop'>" + rebuildExpressionFromAST(fetchConnectingNode,replaceExpressionNodeTag,ast.property) + "</span></span>";
	}

}

function rebuildExpressionFromAST(getNodeById,replaceExpressionNodeTag,ast, isString) {
	window.rebuild = rebuildExpressionFromAST;
	window.jsep = jsep;
	switch(ast.type) {
		case 'BinaryExpression':
			return rebuildExpressionFromAST(getNodeById,replaceExpressionNodeTag, ast.left) + " " + ast.operator + " " + rebuildExpressionFromAST(getNodeById,replaceExpressionNodeTag,ast.right);
		case 'Literal':
			return ast.value;
		case 'Identifier':
			let match = ast.name.match(/_N([0-9]*)/);
			if (false && match != null) {
				// Is a node
				let matchingNode = getNodeById(match[1]);
				return matchingNode.type;
			}
			return ast.name;
			case 'MemberExpression':
				let rootMemberObj = getRootObjectFromMemberExpression(ast);
				let rootMemberExpr = rebuildExpressionFromAST(getNodeById,replaceExpressionNodeTag, rootMemberObj);


				let isNodeMatch = rootMemberExpr.match(/_N([0-9]*)/);
				if (false && isNodeMatch) {
					// Get properties
					let current = ast;
					let stack = [];
					while (current && current.type && current.type == 'MemberExpression') {
						stack.push(rebuildExpressionFromAST(getNodeById,replaceExpressionNodeTag, current.property));
						current = current.object;
					}

					stack.reverse();
					let nodeTag = checkNodeTag(getNodeById,rootMemberExpr,stack);

					if (nodeTag) {
						return replaceExpressionNodeTag(nodeTag, isString);
					}
				}

				var expr =  rebuildExpressionFromAST(getNodeById,replaceExpressionNodeTag,ast.object) + "." + rebuildExpressionFromAST(getNodeById,replaceExpressionNodeTag,ast.property);
				return expr;
				case 'CallExpression':
				var args = '';
				for (var i in ast.arguments) {
					if (args != '')
						args += ",";
					args += rebuildExpressionFromAST(getNodeById,replaceExpressionNodeTag,ast.arguments[i])

				}
					var expr =  rebuildExpressionFromAST(getNodeById,replaceExpressionNodeTag,ast.callee) + "(" + args + ")";
					return expr;
		//	xpression-data-tag'>" + rebuildExpressionFromAST(fetchConnectingNode,replaceExpressionNodeTag,ast.object) +" <span class='node-expression-data-tag__prop'>" + rebuildExpressionFromAST(fetchConnectingNode,replaceExpressionNodeTag,ast.property) + "</span></span>";
	}

	return false;
}
 function __buildExpression(fetchConnectingNode,replaceExpressionNodeTag,node,pinId) {

 	return node.expressions && node.expressions[pinId] || "";


 }
 export function buildExpressionFromValue(expression, fetchNodeById, replaceExpressionNodeTag, isString) {
	 if (typeof expression == 'string')
 	{
 	//	expr = jsep("'" + expression + "'");

 		return rebuildExpressionFromMustache(fetchNodeById,replaceExpressionNodeTag,expression.replace(/(_N[0-9]*\.[A-Za-z0-9\-_]*)/,function(match,p1,p2) { return "{{" + p1  +"}}"; }).replace("{{{{","{{").replace("}}}}","}}"));
 	}

 	let rebuilt = rebuildExpressionFromAST(fetchNodeById,replaceExpressionNodeTag,expression, isString);

 }
export function buildExpression(value, fetchConnectingNode, fieldType,  node, pinId,replaceExpressionNodeTag) {

	//let expression = node.expressions && node.expressions[pinId] || '';
	let expression  =value;
	let expr = expression;

	var isString = fieldType.split('|').indexOf("string") >= 0;

	if (typeof expression == 'string')
	{
	//	expr = jsep("'" + expression + "'");

		return rebuildExpressionFromMustache(fetchConnectingNode,replaceExpressionNodeTag,expr.replace(/(_N[0-9]*\.[A-Za-z0-9\-_]*)/,function(match,p1,p2) { return "{{" + p1  +"}}"; }).replace("{{{{","{{").replace("}}}}","}}"));
	}
	let rebuilt = rebuildExpressionFromAST(fetchConnectingNode,replaceExpressionNodeTag,expr, isString);
	if (rebuilt == false) {
		return expression;
	}
	return rebuilt;

}
export function parseExpression(expression, nodeId = null, fieldName = null, type = null, store = null) {

	if (expression.replace)
		expression = expression.replace(/\{\{(_N[^\}]*)\}\}/i, function (match, p1) { return p1;	});
	try {
		let expr = jsep(expression);
		if (type != null && store != null) {
			if (type == 'number') {
				// No Compound

				let errorMessage = '';
				let allowedStrings = ['+','-','/','*','(',')'];
				let isNumeric = (n) => {
				  return !isNaN(parseFloat(n)) && isFinite(n);
				}

				walkExpression(expr, (ast)=> {

					if (typeof ast === "string" && !isNumeric(ast) && allowedStrings.indexOf(ast) === -1 && ast.indexOf("{{_N") != 0) {
						debugger;

						errorMessage = 'The expression is invalid';
					} else if (ast.type == 'Compound') {
						errorMessage = 'The expression is invalid';
					}
				});

				let state = store.getState();
				if (!state.ui.errors || !state.ui.errors[nodeId] || state.ui.errors[nodeId][fieldName] !== errorMessage) {
					debugger;
					store.dispatch({
						type: 'SET_ERROR_MESSAGE',
						nodeId: nodeId,
						fieldName: fieldName,
						message: errorMessage
					});
				}

			}
		}
		return expr;
	} catch(e) {
		let state = store.getState();
		let errorMessage =  'The expression is invalid';
		if (nodeId != null && fieldName != null && !state.ui.errors || !state.ui.errors[nodeId] || state.ui.errors[nodeId][fieldName] !== errorMessage)
		store.dispatch({
			type: 'SET_ERROR_MESSAGE',
			nodeId: nodeId,
			fieldName: fieldName,
			message: errorMessage
		});
		return expression;
	}

}
export function getTypeFromExpression(expression) {
}

export function stringifyExpression(expression) {
	if (expression == null)
		return null;
	if (typeof expression === 'string')
		return expression;
	if (typeof expression === 'boolean' || typeof expression === 'number')
		return expression;
	if (typeof expression === 'object' && expression['id'])
		return expression['id'];
	let rebuiltExpression = rebuildExpressionFromAST(null,null,expression,true) ;
	if (!rebuiltExpression)
		return expression;
	if (rebuiltExpression.replace)
		rebuiltExpression = rebuiltExpression.replace(/(_N[0-9]*\.[A-Za-z0-9\-_\.\(\)\'.]*)/,function(match,p1,p2) { return "{{" + p1  +"}}"; }).replace("{{{{","{{").replace("}}}}","}}")
	return rebuiltExpression;


}
export function computeOutOffsetByIndex(node,index, nodeRefs, pinRefs) {
	let targetPin = pinRefs[node.nid]&&pinRefs[node.nid]["output"][index];
	let targetNode = nodeRefs[node.nid];
	if (!targetNode)
	return {x:0,y:0};

	//let top = targetPin.getBoundingClientRect().top - targetNode.getBoundingClientRect().top +  (targetPin.getBoundingClientRect().height /2);
	let top = (targetNode.getBoundingClientRect().top +  (targetNode.getBoundingClientRect().height /2)) - targetNode.parentNode.getBoundingClientRect().top;
	if (targetPin)
		top = targetPin.getBoundingClientRect().top - targetNode.getBoundingClientRect().top +  (targetPin.getBoundingClientRect().height /2);

	let outx = targetNode.getBoundingClientRect().right - targetNode.parentNode.getBoundingClientRect().left - 15;
	//
	let outy = node.y + top;
	if (node.fields.out[index] && node.fields.out[index].type == "flow")
	outy +=2;
	return {x:outx, y:outy};

}
