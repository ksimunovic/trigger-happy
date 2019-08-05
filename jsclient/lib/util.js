import React from 'react';
import jsep from 'jsep';

function checkNodeTag( fetchConnectingNode, name, prop ) {
	if ( name && prop ) {
		let match = name.match( /_N([0-9]*)/ );
		if ( null != match ) {
			let matchingNode = fetchConnectingNode( match[1]);
			return {
				node: matchingNode,
				pinId: prop.join( '.' ),
				nodeId: match[1],
				nodeLabel: matchingNode.type,
				pinLabel: prop.join( '.' )
			};
		}
	}
	return false;
}

function rebuildExpressionFromMustache( fetchConnectingNode, replaceExpressionNodeTag, val, isString ) {
	if ( ! val ) {
		return val;
	}
	return val.replace( /{{(.*)}}/g, function( match, p1 ) {
		p1 = p1.replace( '}}', '' );
		let matches = p1.match( /_N([0-9]*)\.(.*)/ );
		if ( null != matches ) {
			let expr = jsep( p1 );
			return rebuildExpressionFromAST( fetchConnectingNode, replaceExpressionNodeTag, expr, true );
		}
		return p1;
	});
}

function getRootObjectFromMemberExpression( obj ) {
	if ( obj.type && 'MemberExpression' == obj.type ) {
		return getRootObjectFromMemberExpression( obj.object );
	}
	return obj;
}

function walkExpression( ast, callback ) {
	if ( ast == undefined ) {
		return;
	}
	callback( ast );
	switch ( ast.type ) {
		case 'BinaryExpression':
			walkExpression( ast.left, callback );
			walkExpression( ast.right, callback );
			walkExpression( ast.operator, callback );
		case 'Literal':
			walkExpression( ast.value, callback );
		case 'Identifier':
			walkExpression( ast.name, callback );
	}
}

function rebuildExpressionFromAST( getNodeById, replaceExpressionNodeTag, ast, isString ) {
	switch ( ast.type ) {
		case 'BinaryExpression':
			return rebuildExpressionFromAST( getNodeById, replaceExpressionNodeTag, ast.left ) + ' ' + ast.operator + ' ' + rebuildExpressionFromAST( getNodeById, replaceExpressionNodeTag, ast.right );
		case 'Literal':
			return ast.value;
		case 'Identifier':
			let match = ast.name.match( /_N([0-9]*)/ );
			if ( false && null != match ) {
				let matchingNode = getNodeById( match[1]);
				return matchingNode.type;
			}
			return ast.name;
		case 'MemberExpression':
			let rootMemberObj = getRootObjectFromMemberExpression( ast );
			let rootMemberExpr = rebuildExpressionFromAST( getNodeById, replaceExpressionNodeTag, rootMemberObj );
			let isNodeMatch = rootMemberExpr.match( /_N([0-9]*)/ );
			if ( false && isNodeMatch ) {
				let current = ast;
				let stack = [];
				while ( current && current.type && 'MemberExpression' == current.type ) {
					stack.push( rebuildExpressionFromAST( getNodeById, replaceExpressionNodeTag, current.property ) );
					current = current.object;
				}
				stack.reverse();
				let nodeTag = checkNodeTag( getNodeById, rootMemberExpr, stack );
				if ( nodeTag ) {
					return replaceExpressionNodeTag( nodeTag, isString );
				}
			}
			return rebuildExpressionFromAST( getNodeById, replaceExpressionNodeTag, ast.object ) + '.' + rebuildExpressionFromAST( getNodeById, replaceExpressionNodeTag, ast.property );
		case 'CallExpression':
			let args = '';
			for ( let i in ast.arguments ) {
				if ( '' != args ) {
					args += ',';
				}
				args += rebuildExpressionFromAST( getNodeById, replaceExpressionNodeTag, ast.arguments[i]);
			}
			return rebuildExpressionFromAST( getNodeById, replaceExpressionNodeTag, ast.callee ) + '(' + args + ')';
	}
	return false;
}
export function buildExpressionFromValue( expression, fetchNodeById, replaceExpressionNodeTag, isString ) {
	if ( 'string' == typeof expression ) {
		return rebuildExpressionFromMustache( fetchNodeById, replaceExpressionNodeTag, expression.replace( /(_N[0-9]*\.[A-Za-z0-9\-_]*)/, function( match, p1, p2 ) {
			return '{{' + p1 + '}}';
		}).replace( '{{{{', '{{' ).replace( '}}}}', '}}' ) );
	}
	return rebuildExpressionFromAST( fetchNodeById, replaceExpressionNodeTag, expression, isString );

}
export function buildExpression( value, fetchConnectingNode, fieldType, node, pinId, replaceExpressionNodeTag ) {
	let expression = value;
	let expr = expression;
	let isString = 0 <= fieldType.split( '|' ).indexOf( 'string' );
	if ( 'string' == typeof expression ) {
		return rebuildExpressionFromMustache( fetchConnectingNode, replaceExpressionNodeTag, expr.replace( /(_N[0-9]*\.[A-Za-z0-9\-_]*)/, function( match, p1, p2 ) {
			return '{{' + p1 + '}}';
		}).replace( '{{{{', '{{' ).replace( '}}}}', '}}' ) );
	}
	let rebuilt = rebuildExpressionFromAST( fetchConnectingNode, replaceExpressionNodeTag, expr, isString );
	if ( false == rebuilt ) {
		return expression;
	}
	return rebuilt;

}
export function parseExpression( expression, nodeId = null, fieldName = null, type = null, store = null ) {
	if ( typeof expression == 'boolean' || typeof expression == 'number' )
		return expression;
	if ( expression.replace ) {
		expression = expression.replace( /\{\{(_N[^\}]*)\}\}/i, function( match, p1 ) {
			return p1;
		});
	}
	try {
		let expr = jsep( expression );
		if ( null != type && null != store ) {
			if ( 'number' == type ) { // No Compound
				let errorMessage = '';
				let allowedStrings = [ '+', '-', '/', '*', '(', ')' ];
				let isNumeric = ( n ) => {
					return ! isNaN( parseFloat( n ) ) && isFinite( n );
				};
				walkExpression( expr, ( ast ) => {
					if ( 'string' === typeof ast && ! isNumeric( ast ) && -1 === allowedStrings.indexOf( ast ) && 0 != ast.indexOf( '{{_N' ) ) {
						errorMessage = 'The expression is invalid';
					} else if ( 'Compound' == ast.type ) {
						errorMessage = 'The expression is invalid';
					}
				});
				let state = store.getState();
				if ( ! state.ui.errors || ! state.ui.errors[nodeId] || state.ui.errors[nodeId][fieldName] !== errorMessage ) {
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
	} catch ( e ) {
		let state = store.getState();
		let errorMessage = 'The expression is invalid';
		if ( null != nodeId && null != fieldName && ! state.ui.errors || ! state.ui.errors[nodeId] || state.ui.errors[nodeId][fieldName] !== errorMessage ) {
			store.dispatch({
				type: 'SET_ERROR_MESSAGE',
				nodeId: nodeId,
				fieldName: fieldName,
				message: errorMessage
			});
		}
		return expression;
	}

}
export function replaceSelfInExpression( expression, nodeId ) {
	let str = stringifyExpression( expression );
	if (typeof str === "string")
		str = str.replace("_self.","_N" + nodeId + ".");
	return parseExpression(str);
}
export function stringifyExpression( expression ) {
	if ( null == expression ) {
		return null;
	}
	if ( 'string' === typeof expression || 'boolean' === typeof expression || 'number' === typeof expression ) {
		return expression;
	}
	if ( 'object' === typeof expression && expression.id ) {
		return expression.id;
	}
	let rebuiltExpression = rebuildExpressionFromAST( null, null, expression, true );
	if ( ! rebuiltExpression ) {
		return expression;
	}
	if ( rebuiltExpression.replace ) {
		rebuiltExpression = rebuiltExpression.replace( /(_N[0-9]*\.[A-Za-z0-9\-_\.\(\)\'.]*)/, function( match, p1, p2 ) {
			return '{{' + p1 + '}}';
		}).replace( '{{{{', '{{' ).replace( '}}}}', '}}' );
	}
	return rebuiltExpression;


}
