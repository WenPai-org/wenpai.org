function BspMore(div1,div2,div3,div4)
{
   d1 = document.getElementById(div1);
   d2 = document.getElementById(div2);
   d3 = document.getElementById(div3);
   d4 = document.getElementById(div4);
   if( d2.style.display == "none" )
   {
      d1.style.display = "none";
      d2.style.display = "block";
	  d3.style.display = "none";
	  d4.style.display = "block";
   }
   /*  this just does a toggle from the original 2 choice code, left here for ref !!
   else
   {
      d1.style.display = "block";
      d2.style.display = "none";
   }
   */
}
function BspLess(div1,div2,div3,div4)
{
   d1 = document.getElementById(div1);
   d2 = document.getElementById(div2);
   d3 = document.getElementById(div3);
   d4 = document.getElementById(div4);
   if( d2.style.display == "block" )
   {
      d1.style.display = "block";
      d2.style.display = "none";
	  d3.style.display = "block";
	  d4.style.display = "none";
   }
}