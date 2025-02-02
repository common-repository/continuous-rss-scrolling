/**
 *     continuous rss scrolling
 *     Copyright (C) 2011 - 2022 www.gopiplus.com
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function crs_scroll() {
	crs_obj.scrollTop = crs_obj.scrollTop + 1;
	crs_scrollPos++;
	if ((crs_scrollPos%crs_heightOfElm) == 0) {
		crs_numScrolls--;
		if (crs_numScrolls == 0) {
			crs_obj.scrollTop = '0';
			crs_content();
		} else {
			if (crs_scrollOn == 'true') {
				crs_content();
			}
		}
	} else {
		/* Speed values: 10 slow, 50 fast */
		var speed = 60 - ( crs_speed * 10 );
		setTimeout("crs_scroll();", speed);
	}
}

var crs_Num = 0;
/*
Creates amount to show + 1 for the scrolling ability to work
scrollTop is set to top position after each creation
Otherwise the scrolling cannot happen
*/
function crs_content() {
	var tmp_vsrp = '';

	w_vsrp = crs_Num - parseInt(crs_numberOfElm);
	if (w_vsrp < 0) {
		w_vsrp = 0;
	} else {
		w_vsrp = w_vsrp%crs_array.length;
	}
	
	// Show amount of vsrru
	var elementsTmp_vsrp = parseInt(crs_numberOfElm) + 1;
	for (i_vsrp = 0; i_vsrp < elementsTmp_vsrp; i_vsrp++) {
		
		tmp_vsrp += crs_array[w_vsrp%crs_array.length];
		w_vsrp++;
	}

	crs_obj.innerHTML 	= tmp_vsrp;
	
	crs_Num 			= w_vsrp;
	crs_numScrolls 	= crs_array.length;
	crs_obj.scrollTop 	= '0';
	// start scrolling
	setTimeout("crs_scroll();", crs_waitseconds * 2000);
}
