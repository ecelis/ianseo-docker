using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CFToIanseo.Objects.Cognito
{
    public class Export
    {
        public Dictionary<string, CognitoTransaction> Transactions { get; set; }

        public List<Archer> Archers { get; set; }
    }
}
