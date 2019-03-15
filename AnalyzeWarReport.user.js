// ==UserScript==
// @name         AnalyzeWarReport
// @namespace    analyze.war.report
// @version      0.4
// @description  try to take over the world!
// @author       Afwas [1337627]
// @match        *://*.torn.com/war.php?step=warreport&warID=*
// @updateURL    https://eu.relentless.pw/AnalyzeWarReport.user.js
// @grant        none
// ==/UserScript==

/* jshint -W097 */
/*global
$
*/

(function() {
    'use strict';
	let header = $('div.title-black').text();
	let re = /#(\d+)/
	let war = header.match(re)[1];
	let factionNames = $('div.faction-names').text().replace('\n', ' ');
	console.log(factionNames);
	re = /^\s?(.+)\s+vs\s+(.+)\s?$/
	let factions = factionNames.match(re);
	console.log(factions);
	let home = factions[1].trim();
	let away = factions[2].trim();
	let warInfo = $('.faction-war-info').text();
	re = /over the sovereignty of (\w{3})/;
	let territory = warInfo.match(re);
	if (territory) {
		territory = territory[1];
	} else {
		re = /claimed sovereignty over (\w{3})/;
		territory = warInfo.match(re)[1];
	}
	console.log(territory);
	let members = [];
    $('.members-list').each(function(index) {
		let faction = '';
		if (index == 0) {
			faction = home;
		} else if (index == 1) {
			faction = away;
		}
		$(this).children('li').each(function() {
			let member = {};
			member.name = $(this).children('div.member').children('a.name').attr('data-placeholder');
			member.team = faction;
			member.faction = $(this).children('div.member').children('a.faction').children('img').attr('title');
			member.level = parseInt($(this).children('div.lvl').text());
			member.points = parseInt($(this).children('div.points').text().replace(/,/g, ''));
			member.joins = parseInt($(this).children('div.joins').text());
			member.clears = parseInt($(this).children('div.knock-off').text());
			members.push(member);
		});

	});
	let csv = '-----\n\n';
	for (let i = 0; i < members.length; i++) {
		let re1 = /\[(\d+)\]$/
		let re2 = /^(.+) \[\d+\]$/
        let member = war + ',' +territory + ',' + members[i].team + ',' + members[i].faction + ',' +
			members[i].name.match(re1)[1] + ',' + members[i].name.match(re2)[1] + ',' +
			members[i].level + ',' + members[i].points + ',' + members[i].joins + ',' +
			members[i].clears + '\n';
		csv += member;
    }
	csv += '\n-----\n';
	console.log(csv);
})();